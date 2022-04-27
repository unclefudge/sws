<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Validator;

use DB;
use Session;
use App\User;
use App\Models\Site\Site;
use App\Models\Site\SiteDoc;
use App\Models\Misc\Equipment\EquipmentLocation;
use App\Http\Requests;
use App\Http\Requests\Site\SiteRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Alert;

class SiteController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('site'))
            return view('errors/404');

        return view('site/list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sitelist()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('site.list'))
            return view('errors/404');

        return view('site/list-basic');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site'))
            return view('errors/404');

        return view('site/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SiteRequest $request)
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site'))
            return view('errors/404');

        $site_request = $request->except('supervisors');

        // Create Site
        $newSite = Site::create($site_request);

        if (request('supervisors'))
            $newSite->supervisors()->sync(request('supervisors'));

        // Create new Equipment Location
        EquipmentLocation::create(['site_id' => $newSite->id, 'status' => 1]);

        $newSite->emailSite('new');

        Toastr::success("Created new site");

        return redirect('site');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $site = Site::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.site', $site))
            return view('errors/404');

        return view('site/show', compact('site'));
    }

    /**
     * Display the settings for the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function showDocs($id)
    {
        $site = Site::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site', $site))
            return view('errors/404');

        return view('site/docs', compact('site'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $site = Site::findorFail($id);
        $old_status = $site->status;

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site', $site))
            return view('errors/404');

        $site_request = request()->except('supervisors');

        // Site recently marked completed
        if ($site_request['status'] == 0 && $site->status != 0) {
            $site_request['completed'] = Carbon::now();
            $location = EquipmentLocation::where('site_id', $site->id)->first();
            if ($location) {
                $location->status = 0;
                $location->save();
            }
        }
        // Site recently reactivated
        if ($site_request['status'] != 0 && $site->status == 0) {
            $location = EquipmentLocation::where('site_id', $site->id)->first();
            if ($location) {
                $location->status = 1;
                $location->save();
            } else
                $location = EquipmentLocation::create(['site_id' => $site->id, 'status' => 1]);
        }
        if ($site_request['status'] != 0)
            $site_request['completed'] = '0000-00-00 00:00:00';

        //dd($site_request);
        $site->update($site_request);

        // Update supervisors for site
        if (request('supervisors'))
            $site->supervisors()->sync(request('supervisors'));
        else
            $site->supervisors()->detach();

        // Email Site if status change
        if ($site->status != $old_status)
            $site->emailSite();

        Toastr::success("Saved changes");

        return redirect('/site/' . $site->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateClient($id)
    {
        $site = Site::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site', $site))
            return view('errors/404');

        $site_request = request()->all();
        $site->update($site_request);
        Toastr::success("Saved changes");

        return redirect('/site/' . $site->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateAdmin($id)
    {
        $site = Site::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.admin', $site))
            return view('errors/404');

        $site_request = request()->all();
        $site_request['contract_sent'] = (request('contract_sent')) ? Carbon::createFromFormat('d/m/Y H:i', request('contract_sent') . '00:00')->toDateTimeString() : null;
        $site_request['contract_signed'] = (request('contract_signed')) ? Carbon::createFromFormat('d/m/Y H:i', request('contract_signed') . '00:00')->toDateTimeString() : null;
        $site_request['deposit_paid'] = (request('deposit_paid')) ? Carbon::createFromFormat('d/m/Y H:i', request('deposit_paid') . '00:00')->toDateTimeString() : null;
        $site_request['completion_signed'] = (request('completion_signed')) ? Carbon::createFromFormat('d/m/Y H:i', request('completion_signed') . '00:00')->toDateTimeString() : null;
        $site_request['council_approval'] = (request('council_approval')) ? Carbon::createFromFormat('d/m/Y H:i', request('council_approval') . '00:00')->toDateTimeString() : null;
        $site_request['engineering_cert'] = (request('engineering_cert')) ? Carbon::createFromFormat('d/m/Y H:i', request('engineering_cert') . '00:00')->toDateTimeString() : null;
        $site_request['construction_rcvd'] = (request('construction_rcvd')) ? Carbon::createFromFormat('d/m/Y H:i', request('construction_rcvd') . '00:00')->toDateTimeString() : null;
        $site_request['hbcf_start'] = (request('hbcf_start')) ? Carbon::createFromFormat('d/m/Y H:i', request('hbcf_start') . '00:00')->toDateTimeString() : null;
        $site->update($site_request);

        Toastr::success("Saved changes");
        return redirect('/site/' . $site->id);
    }

    /**
     * Update the photo on user model resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateLogo(SiteRequest $request, $slug)
    {
        $site = Site::where(compact('slug'))->firstOrFail();

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site', $site))
            return view('errors/404');

        $file = $request->file('photo');
        $path = "filebank/site/" . $site->id;
        $name = "sitephoto." . strtolower($file->getClientOriginalExtension());
        $path_name = $path . '/' . $name;
        $file->move($path, $name);

        Image::make(url($path_name))
            ->fit(740)
            ->save($path_name);

        $site->photo = $path_name;
        $site->save();
        Toastr::success("Saved changes");

        return redirect('/site/' . $site->slug . '/settings/photo');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updatesupervisor($site_id, $super_id)
    {
        $site = Site::findOrFail($site_id);

        // Check authorisation and throw 404 if not
        if (!(Auth::user()->allowed2('edit.site.admin', $site) || Auth::user()->hasAnyPermissionType('preconstruction.planner')))
            return view('errors/404');

        $site->supervisors()->sync([$super_id]);

        Toastr::success("Updated Supervisor");
        if (request()->ajax())
            return response()->json(['success'=> '1']);
        return redirect('/site/' . $site->id);
    }

    /**
     * Get Sites current user is authorised to manage + Process datatables ajax request.
     */
    public function getSites()
    {
        $site_records = Auth::user()->company->sites(request('status'));

        $dt = Datatables::of($site_records)
            ->editColumn('id', function ($site) {
                // Only return link to site if owner of site
                return (Auth::user()->isCompany($site->company_id)) ? '<div class="text-center"><a href="/site/' . $site->id . '"><i class="fa fa-search"></i></a></div>' : '';
            })
            ->editColumn('client_phone', function ($site) {
                $string = '';
                if ($site->client_phone) {
                    $string = $site->client_phone;
                    if ($site->client_phone_desc)
                        $string = $site->client_phone . ' ' . $site->client_phone_desc;
                }
                if ($site->client_phone2) {
                    $string .= '<br>' . $site->client_phone2;
                    if ($site->client_phone2_desc)
                        $string .= ' ' . $site->client_phone2_desc;
                }

                return $string;
            })
            ->addColumn('supervisor', function ($site) {
                return $site->supervisorsSBC();
            })
            ->rawColumns(['id', 'client_phone'])
            ->make(true);

        return $dt;
    }

    /**
     * Get Sites current user is authorised to manage + Process datatables ajax request.
     */
    public function getSiteList()
    {
        $status = request('status');
        if (request('site_group'))
            $site_records = Auth::user()->authSites('view.site.list', $status)->where('company_id', request('site_group'));
        else {
            // If SiteGroup is All (ie 0) and Status (Upcoming or Inactive) then restrict sites to only user own company
            // Child company can't see inactive or upcoming sites for parent
            $site_records = (request('site_group') == '0' && $status != 1) ?
                Auth::user()->authSites('view.site.list', $status)->where('company_id', Auth::user()->company_id) :
                Auth::user()->authSites('view.site.list', $status);
        }

        $dt = Datatables::of($site_records)
            ->editColumn('name', function ($site) {
                return $site->nameClient;
            })
            ->editColumn('client_phone', function ($site) {
                $string = '';
                if ($site->client_phone) {
                    $string = $site->client_phone;
                    if ($site->client_phone_desc)
                        $string = $site->client_phone . ' ' . $site->client_phone_desc;
                }
                if ($site->client_phone2) {
                    $string .= '<br>' . $site->client_phone2;
                    if ($site->client_phone2_desc)
                        $string .= ' ' . $site->client_phone2_desc;
                }

                return $string;
            })
            ->addColumn('supervisor', function ($site) {
                return $site->supervisorsSBC();
            })
            ->rawColumns(['id', 'client_phone'])
            ->make(true);

        return $dt;
    }

    /**
     * Get Site Docs current user is authorised to manage + Process datatables ajax request.
     */
    public function getSiteDocs()
    {
        //dd($allowedSites);
        $type = request('type');
        if ($type == 'ALL')
            $records = DB::table('site_docs as d')
                ->select(['d.id', 'd.type', 'd.site_id', 'd.attachment', 'd.name', 's.id as sid', 's.name as site_name'])
                ->join('sites as s', 'd.site_id', '=', 's.id')
                ->where('site_id', request('site_id'))
                ->where('d.status', '1');
        else
            $records = DB::table('site_docs as d')
                ->select(['d.id', 'd.type', 'd.site_id', 'd.attachment', 'd.name', 's.id as sid', 's.name as site_name'])
                ->join('sites as s', 'd.site_id', '=', 's.id')
                ->where('d.type', $type)
                ->where('site_id', request('site_id'))
                ->where('d.status', '1');

        //dd($records);

        $dt = Datatables::of($records)
            ->editColumn('id', '<div class="text-center"><a href="/filebank/site/{{$site_id}}/docs/{{$attachment}}"><i class="fa fa-file-text-o"></i></a></div>')
            ->addColumn('action', function ($doc) {
                $record = SiteDoc::find($doc->id);
                $actions = '';
/*
                if ($doc->type == 'PLAN') {
                    if (Auth::user()->allowed2('edit.site.doc', $record))
                        $actions .= '<a href="/site/doc/' . $doc->id . '" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>';
                    if (Auth::user()->allowed2('del.site.doc', $record))
                        $actions .= '<button class="btn dark btn-xs sbold uppercase margin-bottom btn-delete " data-remote="/site/doc/' . $doc->id . '" data-name="' . $doc->name . '"><i class="fa fa-trash"></i></button>';
                } else {
                    if (Auth::user()->allowed2('edit.safety.doc', $record))
                        $actions .= '<a href="/site/doc/' . $doc->id . '" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>';
                    if (Auth::user()->allowed2('del.safety.doc', $record))
                        $actions .= '<button class="btn dark btn-xs sbold uppercase margin-bottom btn-delete " data-remote="/site/doc/' . $doc->id . '" data-name="' . $doc->name . '"><i class="fa fa-trash"></i></button>';
                }
*/
                return $actions;
            })
            ->rawColumns(['id', 'action'])
            ->make(true);

        return $dt;
    }


    /**
     * Get basic Site details.
     */
    public function getSiteDetails($id)
    {
        return Site::findOrFail($id);
    }

    /**
     * Get Site supervisor (first).
     */
    public function getSiteSuper($id)
    {
        return Site::findOrFail($id)->supervisors->first();
    }

    /**
     * Get basic Site details.
     */
    /*
    public function getSiteOwner($id)
    {
        $site = Site::find($id);
        return $site->client->clientOfCompany;
    }*/


    /**
     * Process Site Check-in.
     *
     * @return \Illuminate\Http\Response
     */
    /*
    public function processCheckin(SiteCheckinRequest $request, $slug)
    {
        $site = Site::where(compact('slug'))->firstOrFail();

        if ($request->has('safe_site'))
            $site->attendance()->save(new SiteAttendance(['safe_site' => '1']));
        else {
            if ($request->has('checkinTrade')) {
                $worksite = Site::find($site->id);

                return view('site/checkinTradeFail', compact(['worksite']));
            }

            $site->attendance()->save(new SiteAttendance(['safe_site' => '0']));

            // Create Hazard + attach to site
            if ($request->has('action_required'))
                $hazard = $site->hazards()->save(new SiteHazard($request->only('action_required', 'reason', 'location', 'rating')));
            else
                $hazard = $site->hazards()->save(new SiteHazard($request->only('reason', 'location', 'rating')));

            //Create action taken + attach to hazard
            if ($hazard) {
                $action = Action::create(['action' => $request->get('action'), 'table' => 'site_hazards', 'table_id' => $hazard->id]);
                $hazard->touch(); // update timestamp

                // Handle attached Photo or Video
                if ($request->hasFile('media'))
                    $hazard->saveAttachedMedia($request->file('media'));

                // Email hazard
                $hazard->emailHazard($action);
            }
        }

        // if Today add them to Roster if Company is on Planer but user not on Roster
        $today = Carbon::now()->format('Y-m-d');
        if ($site->isCompanyOnPlanner(Auth::user()->company_id, $today) && !$site->isUserOnRoster(Auth::user()->id, $today)) {
            $newRoster = SiteRoster::create(array(
                'site_id'    => $site->id,
                'user_id'    => Auth::user()->id,
                'date'       => $today . ' 00:00:00',
                'created_by' => '1',
                'updated_by' => '1',
            ));
        }

        Toastr::success("Checked in");

        //$worksite = $site;
        //dd($site);
        return redirect('/dashboard');
    }*/

}
