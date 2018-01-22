<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Validator;

use DB;
use PDF;
use Mail;
use Input;
use Session;
use App\User;
use App\Models\Site\Site;
use App\Models\Site\SiteAsbestos;
use App\Models\Site\SiteAsbestosAction;
use App\Models\Misc\Action;
use App\Models\Company\Company;
use App\Models\Comms\Todo;
use App\Models\Comms\TodoUser;
use App\Http\Requests;
use App\Http\Requests\Site\SiteAsbestosRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;

/**
 * Class SiteAsbestosController
 * @package App\Http\Controllers\Site
 */
class SiteAsbestosController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('site.asbestos'))
            return view('errors/404');

        return view('site/asbestos/list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.asbestos'))
            return view('errors/404');

        $site_id = '';
        if (Session::has('siteID'))
            $site_id = Site::where('code', Session::get('siteID'))->first()->id;

        return view('site/asbestos/create', compact('site_id'));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $asb = SiteAsbestos::findOrFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.site.asbestos', $asb))
            return view('errors/404');

        return view('site/asbestos/show', compact('asb'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SiteAsbestosRequest $request)
    {
        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.asbestos'))
            return view('errors/404');

        // Additional Complex Custom Validation for Inspection + Supervisor fields
        $validator = Validator::make($request->all(), []);
        $validator->after(function ($validator) {
            if (request()->get('friable') == '0' && request()->get('amount_over') == '1' && request()->get('inspection') != '1')
                $validator->errors()->add('inspection', 'The inspection confirmation field must be YES');
            if (request()->get('friable') == '0' && request()->get('amount_over') == '1' && request()->get('supervisor_id') == '')
                $validator->errors()->add('supervisor_id', 'You must select a Supervisor');
        });

        if ($validator->fails()) {
            return redirect('site/asbestos/create')->withErrors($validator)->withInput();
        }

        $asb_request = removeNullValues($request->all());

        // Type Other Specificed
        if ($request->get('type') == 'other')
            $asb_request['type'] = $request->get('type_other');

        // Dates
        $asb_request['date_from'] = Carbon::createFromFormat('d/m/Y H:i', $request->get('date_from') . '00:00')->toDateTimeString();
        $asb_request['date_to'] = Carbon::createFromFormat('d/m/Y H:i', $request->get('date_to') . '00:00')->toDateTimeString();

        // Equipment flags
        $equipment = $request->get('equip');
        if ($equipment) {
            foreach ($equipment as $e) {
                if ($e == 'equip_overalls') $asb_request['equip_overalls'] = 1;
                if ($e == 'equip_mask') $asb_request['equip_mask'] = 1;
                if ($e == 'equip_gloves') $asb_request['equip_gloves'] = 1;
                if ($e == 'equip_half_face') $asb_request['equip_half_face'] = 1;
                if ($e == 'equip_full_face') $asb_request['equip_full_face'] = 1;
            }
        }

        // Equipment flags
        $methods = $request->get('method');
        if ($methods) {
            foreach ($methods as $m) {
                if ($m == 'method_fencing') $asb_request['method_fencing'] = 1;
                if ($m == 'method_signage') $asb_request['method_signage'] = 1;
                if ($m == 'method_water') $asb_request['method_water'] = 1;
                if ($m == 'method_barriers') $asb_request['method_barriers'] = 1;
                if ($m == 'method_plastic') $asb_request['method_plastic'] = 1;
                if ($m == 'method_vacuum') $asb_request['method_vacuum'] = 1;
            }
        }

        //dd($asb_request);

        // Create Site Asbestos
        $newAsb = SiteAsbestos::create($asb_request);
        if ($newAsb) {
            $action = Action::create(['action' => 'Created Notification', 'table' => 'site_asbestos', 'table_id' => $newAsb->id]);
            $newAsb->touch(); // update timestamp
            // Email notification
            $newAsb->emailNotification();
        }

        Toastr::success("Created notification");

        return redirect('/site/asbestos/');
    }


    /**
     * Update Status the specified resource in storage.
     */
    public function updateStatus(Request $request, $id, $status)
    {
        $asb = SiteAsbestos::findOrFail($id);
        $old_status = $asb->status;
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.asbestos', $asb))
            return view('errors/404');

        // Update Status
        if ($status != $old_status)
            $asb->updateStatus($status);

        return redirect('site/asbestos/' . $asb->id);
    }


    /**
     * Get Asbestos Reports current user is authorised to manage + Process datatables ajax request.
     */
    public function getReports(Request $request)
    {
        $site_list = Auth::user()->authSites('view.site.asbestos')->pluck('id')->toArray();
        $records = DB::table('site_asbestos AS a')
            ->select(['a.id', 'a.site_id', 'a.amount', 'a.friable', 'a.type', 'a.amount', 'a.date_from', 'a.date_to', 'a.status', 'a.company_id', 'a.updated_at',
                's.name as sitename', 's.code'])
            ->join('sites AS s', 'a.site_id', '=', 's.id')
            ->whereIn('a.site_id', $site_list)
            ->where('a.status', $request->get('status'));

        $dt = Datatables::of($records)
            ->editColumn('id', '<div class="text-center"><a href="/site/asbestos/{{$id}}"><i class="fa fa-search"></i></a></div>')
            ->editColumn('updated_at', function ($doc) {
                return (new Carbon($doc->updated_at))->format('d/m/Y');
            })
            ->addColumn('proposed_dates', function ($doc) {
                return (new Carbon($doc->date_from))->format('d M') . ' - ' . (new Carbon($doc->date_to))->format('d M');
            })
            ->addColumn('supervisor', function ($doc) {
                $s = Site::find($doc->site_id);

                return ($s->supervisorsSBC());
            })
            ->addColumn('action', function ($doc) {
                if ($doc->status && Auth::user()->allowed2('edit.site.asbestos', $doc))
                    return '<a href="/site/asbestos/' . $doc->id . '" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>';

                return '<a href="/site/asbestos/' . $doc->id . '" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-search"></i> View</a>';
            })
            ->rawColumns(['id', 'action'])
            ->make(true);

        return $dt;
    }
}
