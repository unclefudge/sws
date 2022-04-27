<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Validator;

use DB;
use PDF;
use Mail;
use Session;
use App\Models\Site\Site;
use App\Models\Site\SiteHazard;
use App\Models\Misc\Action;
use App\Http\Requests;
use App\Http\Requests\Site\SiteHazardRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;

/**
 * Class SiteHazardController
 * @package App\Http\Controllers
 */
class SiteHazardController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->hasAnyPermissionType('site.hazard'))
            return view('errors/404');

        return view('site/hazard/list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.hazard'))
            return view('errors/404');

        $site_id = (Session::has('siteID')) ? Session::get('siteID') : '';

        return view('site/hazard/create', compact('site_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SiteHazardRequest $request)
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.hazard'))
            return view('errors/404');

        $my_request = $request->except('action');
        //dd($my_request);
        $hazard = SiteHazard::create($my_request);

        //Create action taken + attach image to issue
        if ($hazard) {
            $action = Action::create(['action' => 'Reported Hazard', 'table' => 'site_hazards', 'table_id' => $hazard->id]);
            $action = Action::create(['action' => $request->get('action'), 'table' => 'site_hazards', 'table_id' => $hazard->id]);
            $hazard->touch(); // update timestamp

            // Handle attached Photo or Video
            if ($request->hasFile('media'))
                $hazard->saveAttachedMedia($request->file('media'));

            // Email hazard
            $hazard->emailHazard($action);

        }
        Toastr::success("Lodged hazard");
        $worksite = Site::findOrFail($my_request['site_id']);

        return redirect('dashboard');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $hazard = SiteHazard::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.site.hazard', $hazard))
            return view('errors/404');

        //return view('site/hazard/list');
        return view('site/hazard/show', compact('hazard'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $hazard = SiteHazard::findorFail($id);
        $old_status = $hazard->status;

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('del.site.hazard', $hazard))
            return view('errors/404');

        $hazard->update($request->all());

        if ($hazard->status != $old_status) {
            $action = Action::create(['action' => 'Hazard has been resolved', 'table' => 'site_hazards', 'table_id' => $hazard->id]);
            $hazard->emailAction($action, 'important');
        }

        return view('site/hazard/show', compact('hazard'));
    }

    /**
     * Update Status the specified resource in storage.
     */
    public function updateStatus(Request $request, $id, $status)
    {
        $hazard = SiteHazard::findorFail($id);
        $old_status = $hazard->status;

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.hazard', $hazard))
            return view('errors/404');

        // Update Status
        if ($status != $old_status)
            $hazard->updateStatus($status);

        return view('site/hazard/show', compact('hazard'));
    }

    /**
     * Get Sites current user is authorised to manage + Process datatables ajax request.
     */
    public function getHazards(Request $request)
    {
        $company_ids = (request('site_group')) ? [request('site_group')] : [Auth::user()->company_id, Auth::user()->company->reportsTo()->id];
        $hazard_ids = Auth::user()->siteHazards($request->get('status'))->pluck('id')->toArray();
        $hazard_records = SiteHazard::select([
            'site_hazards.id', 'site_hazards.site_id', 'site_hazards.created_by',
            DB::raw('DATE_FORMAT(site_hazards.created_at, "%d/%m/%y") AS nicedate'),
            DB::raw('DATE_FORMAT(site_hazards.resolved_at, "%d/%m/%y") AS nicedate2'),
            DB::raw('CONCAT(users.firstname, " ", users.lastname) AS fullname'),
            'site_hazards.reason', 'site_hazards.action_required', 'site_hazards.attachment',
            'site_hazards.status', 'site_hazards.location', 'site_hazards.rating', 'site_hazards.source',
            'sites.name', 'sites.code', 'sites.company_id',
        ])
            ->join('sites', 'site_hazards.site_id', '=', 'sites.id')
            ->join('users', 'site_hazards.created_by', '=', 'users.id')
            ->where('site_hazards.status', '=', $request->get('status'))
            ->whereIn('site_hazards.id', $hazard_ids)
            ->whereIn('sites.company_id', $company_ids);


        //->orderBy('site_hazards.created_at', 'DESC');

        $dt = Datatables::of($hazard_records)
            ->addColumn('view', function ($issue) {
                return ('<div class="text-center"><a href="/site/hazard/' . $issue->id . '"><i class="fa fa-search"></i></a></div>');
            })
            ->editColumn('name', function ($issue) {
                return $issue->site->nameClient;
            })
            ->addColumn('supervisor', function ($issue) {
                return ($issue->site->supervisorsSBC());
            })
            ->editColumn('action_required', function ($issue) {
                return ($issue->action_required) ? 'Yes' : 'No';
            })
            ->editColumn('rating', function ($issue) {
                if ($issue->rating == 3) return 'High';
                if ($issue->rating == 2) return 'Med';
                if ($issue->rating == 1) return 'Low';

                return 'None';
            })
            ->editColumn('nicedate2', function ($issue) {
                return ($issue->nicedate2 == '00/00/00') ? '' : $issue->nicedate2;
            })
            ->editColumn('attachment', function ($issue) {
                if ($issue->attachment && file_exists('filebank/site/' . $issue->site_id . '/hazard/' . $issue->attachment)) {
                    return '<a href="/filebank/site/' . $issue->site_id . '/hazard/' . $issue->attachment . '" data-lity class="html5lightboxXXXX btn btn-xs blue"><i class="fa fa-picture-o"></a>';
                }

                return '';
            })
            //->filterColumn('fullname', 'whereRaw', "CONCAT(users . firstname, ' ', users . lastname) like ? ", [" % $1 % "])
            ->rawColumns(['view', 'action', 'attachment'])
            ->make(true);

        return $dt;
    }
}
