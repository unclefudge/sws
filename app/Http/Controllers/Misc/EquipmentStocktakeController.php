<?php

namespace App\Http\Controllers\Misc;

use Illuminate\Http\Request;
use Validator;

use DB;
use Session;
use App\Models\Misc\Equipment\Equipment;
use App\Models\Misc\Equipment\EquipmentLocation;
use App\Models\Misc\Equipment\EquipmentLocationItem;
use App\Models\Misc\Equipment\EquipmentStocktake;
use App\Models\Misc\Equipment\EquipmentStocktakeItem;
use App\Models\Misc\Equipment\EquipmentLost;
use App\Models\Misc\Equipment\EquipmentLog;
use App\Models\Site\Site;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Alert;

class EquipmentStocktakeController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('equipment'))
            return view('errors/404');

        return view('misc/equipment/list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (preg_match('/^newloc/', $id)) {
            // Create New Location for Site ID
            list($crap, $site_id) = explode('-', $id);
            $location = new EquipmentLocation(['site_id' => $site_id]);
            $location->save();
        } else
            $location = EquipmentLocation::find($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.equipment.stocktake', $location))
            return view('errors/404');

        foreach (EquipmentLocation::where('status', 1)->where('notes', null)->where('site_id', '<>', '25')->get() as $loc)
            $sites[$loc->id] = $loc->name;
        // Active Site but current no equipment
        foreach (Auth::user()->authSites('view.site', 1) as $site) {
            $name = "$site->suburb ($site->name)";
            if (!in_array($name, $sites) && !in_array($site->id, [25, 92, 366])) // Store, Conference, OnLeave
                $sites["newloc-$site->id"] = "$name";
        }
        asort($sites);
        $sites = ['1' => 'CAPE COD STORE'] + $sites;

        foreach (EquipmentLocation::where('status', 1)->where('notes', null)->where('site_id', null)->get() as $loc)
            $others[$loc->id] = $loc->name;
        asort($others);


        $items = [];
        if ($location) {
            // Get items then filter out 'deleted'
            $all_items = EquipmentLocationItem::where('location_id', $location->id)->get();
            //dd($all_items);
            $items = $all_items->filter(function ($item) {
                if ($item->equipment->status) return $item;
            });
        }

        $items = ($items) ? $items->sortBy('item_name') : [];
        $items2 = [];
        $items3 = [];

        return view('misc/equipment/stocktake', compact('location', 'sites', 'others', 'items', 'items2', 'items3'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showStocktake($id)
    {
        $stock = EquipmentStocktake::find($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.equipment.stocktake', $stock))
            return view('errors/404');

        return view('misc/equipment/stocktake-show', compact('stock'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        //dd(request()->all());
        $location = EquipmentLocation::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.equipment.stocktake', $location))
            return view('errors/404');

        $extra_items = [];

        $stocktake = new EquipmentStocktake(['location_id' => $location->id]);
        $stocktake->save();
        $passed_all = 1;;

        // Get items then filter out 'deleted'
        $all_items = EquipmentLocationItem::where('location_id', $location->id)->get();
        $items = $all_items->filter(function ($item) {
            if ($item->equipment->status) return $item;
        });

        $exclude = (request('exclude')) ? request('exclude') : [];

        //dd(request()->all());
        // Check if current qty matches DB
        foreach ($items as $item) {
            $qty_now = request($item->id . '-qty');
            $passed_item = 1;
            $stocktake_item = new EquipmentStocktakeItem(['stocktake_id' => $stocktake->id, 'equipment_id' => $item->equipment_id, 'qty_expect' => $item->qty, 'qty_actual' => $qty_now]);
            if (($location->site_id == 25 && !in_array($item->id, $exclude)) || ($location->site_id != 25 && in_array($item->id, $exclude))) {
                // Ignore excluded items. For CapeCod Store 'excluded' items are actually 'included' - reverse
                $stocktake_item->qty_actual = $passed_item = null;
            } else {
                if ($item->qty > $qty_now) {
                    // Missing items
                    $passed_all = $passed_item = 0;
                    // There were less items found at location then expected so
                    // check if 'extra' items are elsewhere and any none 'extra' mark them as missing
                    if (($item->qty - $qty_now) > $item->equipment->total_excess)
                        $this->lostItem($item->location_id, $item->equipment_id, ($item->qty - $qty_now - $item->equipment->total_excess));
                } elseif ($item->qty < $qty_now) {
                    // Extra items
                    $extra_items[$item->equipment_id] = ($qty_now - $item->qty);
                }

                // Update altered qty at location
                if ($item->qty != $qty_now) {
                    if ($qty_now) {
                        $item->qty = $qty_now;
                        $item->save();
                    } else
                        $item->delete();
                }
            }

            // Save Stocktake
            $stocktake_item->passed = $passed_item;
            $stocktake_item->save();
        }

        $stocktake->passed = $passed_all;
        $stocktake->save();

        // Add extra items to location
        for ($i = 1; $i <= 10; $i ++) {
            if (request("$i-extra_qty") && request("$i-extra_id")) {
                $equip = Equipment::findOrFail(request("$i-extra_id"));
                $extra_items[$equip->id] = request("$i-extra_qty");

                // Add item to location
                $location->items()->save(new EquipmentLocationItem(['location_id' => $location->id, 'equipment_id' => $equip->id, 'qty' => request("$i-extra_qty")]));

                // Add item to stocktake
                $stocktake_item = new EquipmentStocktakeItem(['stocktake_id' => $stocktake->id, 'equipment_id' => $equip->id, 'qty_expect' => 0, 'qty_actual' => request("$i-extra_qty"), 'passed' => 1]);
                $stocktake_item->save();
            }
        }

        // For the 'extra' items above the expected amount determine if they were missing from another site
        // a) if missing the mark as found
        // b) if not missing mark as excess
        if (count($extra_items)) {
            foreach ($extra_items as $equip_id => $amount) {
                $extra_amount = $amount;
                $lost_items = EquipmentLost::where('equipment_id', $equip_id)->orderBy('created_at', 'DESC')->get();
                if ($lost_items) {
                    foreach ($lost_items as $lost) {
                        if ($extra_amount) {
                            if ($lost->qty > $extra_amount) {
                                // More lost items then found so subtract only found amount
                                $lost->decrement('qty', $extra_amount);
                                $log = new EquipmentLog(['equipment_id' => $lost->equipment_id, 'qty' => $extra_amount, 'action' => 'F', 'notes' => "Found $extra_amount items at $location->name"]);
                                $extra_amount = 0;
                                break;
                            } else {
                                // Found more items then are actually lost so delete full amount from lost item.
                                $extra_amount = $extra_amount - $lost->qty;
                                $log = new EquipmentLog(['equipment_id' => $lost->equipment_id, 'qty' => $lost->qty, 'action' => 'F', 'notes' => "Found $lost->qty items at $location->name"]);
                                $lost->delete();
                            }
                            $log->save();
                        }
                    }
                    if ($extra_amount) {
                        $equip = Equipment::find($equip_id);
                        if (($equip->total - ($equip->purchased - $equip->disposed)) > 0)
                            Toastr::warning("Item: $equip->name increased above actual number of purchased items.");
                    }
                }
            }
        }
        if (!$passed_all)
            Toastr::error("Some items marked as missing");
        Toastr::success("Saved changes");

        return redirect("/equipment/stocktake/$location->id");
    }


    /**
     * Lost item
     */
    public function lostItem($location_id, $equipment_id, $qty)
    {
        $location = EquipmentLocation::findOrFail($location_id);

        $existing = EquipmentLost::where('location_id', $location_id)->where('equipment_id', $equipment_id)->first();
        if ($existing) {
            // Update existing lost qty
            $existing->qty = $existing->qty + $qty;
            $existing->save();
        } else {
            // Create Lost item
            $newLost = new EquipmentLost(['location_id' => $location_id, 'equipment_id' => $equipment_id, 'qty' => $qty]);
            $newLost->save();
        }

        // Create New Transaction for log
        $log = new EquipmentLog(['equipment_id' => $equipment_id, 'qty' => $qty, 'action' => 'M', 'notes' => "Missing $qty items from $location->name"]);
        $log->save();
    }

    /**
     * Get Stocktake + Process datatables ajax request.
     */
    public function getStocktake()
    {
        //dd(request('location_id'));
        $stocktake = EquipmentStocktake::where('location_id', request('location_id'));

        $dt = Datatables::of($stocktake)
            ->editColumn('id', function ($stock) {
                return '<div class="text-center"><a href="/equipment/stocktake/view/' . $stock->id . '"><i class="fa fa-search"></i></a></div>';
            })
            ->editColumn('created_at', function ($stock) {
                return $stock->created_at->format('d/m/Y');
            })
            ->editColumn('created_by', function ($stock) {
                return $stock->user->name;
            })
            ->addColumn('summary', function ($stock) {
                return ($stock->summary());
            })
            ->editColumn('passed', function ($stock) {
                return ($stock->passed) ? 'Yes' : 'No';
            })
            ->rawColumns(['id', 'created_by', 'summary'])
            ->make(true);

        return $dt;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        //
    }
}
