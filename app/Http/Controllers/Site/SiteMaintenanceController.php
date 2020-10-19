<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Validator;

use DB;
use PDF;
use Mail;
use Session;
use App\User;
use App\Models\Site\Planner\Task;
use App\Models\Site\Planner\Trade;
use App\Models\Site\Site;
use App\Models\Site\SiteMaintenance;
use App\Models\Site\SiteMaintenanceItem;
use App\Models\Site\SiteMaintenanceDoc;
use App\Models\Site\SiteMaintenanceCategory;
use App\Models\Site\Planner\SitePlanner;
use App\Models\Misc\Action;
use App\Models\Company\Company;
use App\Models\Comms\Todo;
use App\Models\Comms\TodoUser;
use App\Jobs\SiteQaPdf;
use App\Http\Requests;
use App\Http\Requests\Site\SiteQaRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;

/**
 * Class SiteMaintenanceController
 * @package App\Http\Controllers\Site
 */
class SiteMaintenanceController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('site.maintenance'))
            return view('errors/404');

        $requests = Auth::user()->maintenanceRequests(2);
        $request_ids = ($requests) ? $requests->pluck('id')->toArray() : [];

        $under_review = DB::table('site_maintenance AS m')
            ->select(['m.id', 'm.site_id', 'm.code', 'm.supervisor', 'm.completed', 'm.reported', 'm.warranty', 'm.goodwill', 'm.category_id', 'm.status', 'm.updated_at', 'm.created_at',
                DB::raw('DATE_FORMAT(m.created_at, "%d/%m/%y") AS created_date'),
                DB::raw('DATE_FORMAT(m.completed, "%d/%m/%y") AS completed_date'),
                DB::raw('DATE_FORMAT(m.updated_at, "%d/%m/%y") AS updated_date'),
                's.code as sitecode', 's.name as sitename'])
            ->join('sites AS s', 'm.site_id', '=', 's.id')
            ->whereIn('m.id', $request_ids)
            ->where('m.status', 2)->get();

        return view('site/maintenance/list', compact('under_review'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.maintenance'))
            return view('errors/404');

        return view('site/maintenance/create');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $main = SiteMaintenance::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.site.maintenance', $main))
            return view('errors/404');

        if ($main->step == 2)
            return view('site/maintenance/photos', compact('main'));
        elseif ($main->step == 3)
            return view('site/maintenance/review', compact('main'));
        else
            return view('site/maintenance/show', compact('main'));
    }

    /**
     * Edit the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $main = SiteMaintenance::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.maintenance', $main))
            return view('errors/404');

        if ($main->step == 2)
            return view('site/maintenance/photos', compact('main'));
        elseif ($main->step == 3)
            return view('site/maintenance/review', compact('main'));
        else
            return view('site/maintenance/show', compact('main'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.maintenance'))
            return view('errors/404');

        $rules = ['site_id' => 'required', 'supervisor' => 'required', 'completed' => 'required', 'reported' => 'required', 'item1' => 'required'];
        $mesg = [
            'site_id.required'    => 'The site field is required.',
            'supervisor.required' => 'The supervisor field is required.',
            'completed.required'  => 'The prac completed field is required.',
            'reported.required'   => 'The reported field is required.',
            'item1.required'      => 'The item field is required.'];
        request()->validate($rules, $mesg); // Validate

        $site_id = request('site_id');
        $main_request = request()->except('multifile');
        $main_request['completed'] = (request('completed')) ? Carbon::createFromFormat('d/m/Y H:i', request('completed') . '00:00')->toDateTimeString() : null;
        $main_request['reported'] = (request('reported')) ? Carbon::createFromFormat('d/m/Y H:i', request('reported') . '00:00')->toDateTimeString() : null;
        $main_request['status'] = 2; // set new request to 'Under Review'
        $main_request['step'] = 2; // set new request to step 2 'Add Photos'

        //dd($main_request);
        // Create Maintenance Request
        $newMain = SiteMaintenance::create($main_request);
        $newMain->code = $newMain->id + 1352; // Generate new incremental code with 1352 off set to maintain sequence
        $newMain->save();
        $action = Action::create(['action' => "Maintenance Request created", 'table' => 'site_maintenance', 'table_id' => $newMain->id]);


        // Add Request Items
        SiteMaintenanceItem::create(['main_id' => $newMain->id, 'name' => request("item1"), 'order' => 1, 'status' => 0]);
        /*$order = 1;
        for ($i = 1; $i <= 25; $i ++) {
            if (request("item$i")) {
                SiteMaintenanceItem::create(['main_id' => $newMain->id, 'name' => request("item$i"), 'order' => $order, 'status' => 0]);
                $order ++;
            }
        }*/
        //dd($main_request);

        // Update Site Status
        $site = Site::find($site_id);
        $site->status = 2;
        $site->save();

        Toastr::success("Created Maintenance Request");

        return redirect('/site/maintenance/' . $newMain->id . '/edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function photos($id)
    {
        $main = SiteMaintenance::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.maintenance'))
            return view('errors/404');

        $main->step = 3;
        $main->save();

        // Create ToDoo for assignment to Supervisor
        $todo_request = [
            'type'       => 'maintenance',
            'type_id'    => $main->id,
            'name'       => 'Site Maintenance Client Request - ' . $main->site->name,
            'info'       => 'Please review request and assign to supervisor',
            'due_at'     => nextWorkDate(Carbon::today(), '+', 2)->toDateTimeString(),
            'company_id' => $main->site->owned_by->id,
        ];

        $user_list = [7]; // Gary
        $todo = Todo::create($todo_request);
        $todo->assignUsers($user_list);

        Toastr::success("Updated Request");

        return redirect('site/maintenance/' . $main->id . '/edit');
    }


    /**
     * Update the specified resource in storage.
     */
    public function review($id)
    {
        $main = SiteMaintenance::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!(Auth::user()->allowed2('add.site.maintenance') || Auth::user()->allowed2('edit.site.maintenance', $main)))
            return view('errors/404');

        $rules = ['site_id' => 'required', 'supervisor' => 'required', 'completed' => 'required', 'item1' => 'required'];
        $mesg = [
            'site_id.required'    => 'The site field is required.',
            'supervisor.required' => 'The supervisor field is required.',
            'completed.required'  => 'The prac completed field is required.',
            'item1.required'      => 'The item field is required.'];

        if (Auth::user()->allowed2('sig.site.maintenance', $main)) {
            $rules = $rules + ['super_id' => 'required'];
            $mesg = $mesg + ['super_id.required' => 'The assign to field is required'];
            //$visit_date = Carbon::createFromFormat('d/m/Y H:i:s', request('visit_date') . ' 00:00:00');
        }
        request()->validate($rules, $mesg); // Validate
        //dd(request()->all());

        $main_request = request()->except('completed');

        // Verify reported date
        if (request('reported')) {
            if (preg_match("/(\d{2})\/(\d{2})\/(\d{4})$/", request('reported'), $matches)) {
                list($dd, $mm, $yyyy) = explode('/', request('reported'));
                if (checkdate($mm, $dd, $yyyy))
                    $main_request['reported'] = Carbon::createFromFormat('d/m/Y H:i:s', request('reported') . ' 00:00:00');
                else
                    return back()->withErrors(['reported' => "Invalid reported date. Required format dd/mm/yyyy"]);
            } else
                return back()->withErrors(['reported' => "Invalid reported date. Required format dd/mm/yyyy"]);
        }

        // Verify prac completed date
        if (request('completed')) {
            if (preg_match("/(\d{2})\/(\d{2})\/(\d{4})$/", request('completed'), $matches)) {
                list($dd, $mm, $yyyy) = explode('/', request('completed'));
                if (checkdate($mm, $dd, $yyyy))
                    $main_request['completed'] = Carbon::createFromFormat('d/m/Y H:i:s', request('completed') . ' 00:00:00');
                else
                    return back()->withErrors(['completed' => "Invalid Prac Completed date. Required format dd/mm/yyyy"]);
            } else
                return back()->withErrors(['completed' => "Invalid Prac Completed date. Required format dd/mm/yyyy"]);
        }

        //dd($main_request);

        if (Auth::user()->allowed2('sig.site.maintenance', $main)) {
            $super = User::find(request('super_id'));
            $main_request['step'] = 4;
            $main_request['status'] = 1; // Set status to active
            $action = Action::create(['action' => "$super->name assigned to supervise request ", 'table' => 'site_maintenance', 'table_id' => $main->id]);
            Toastr::success("Assigned Request");

            // Delete Todoo
            $main->closeToDo(Auth::user());

            // Add to Client Visit planner
            /*
            $newPlanner = SitePlanner::create(array(
                'site_id'     => $main->site_id,
                'from'        => $visit_date->format('Y-m-d') . ' 00:00:00',
                'to'          => $visit_date->format('Y-m-d') . ' 00:00:00',
                'days'        => 1,
                'entity_type' => 'c',
                'entity_id'   => request('company_id'),
                'task_id'     => '524' // Client Visit
            ));*/

        }

        // Update Items
        $item1 = $main->items->first();
        if ($item1->name != request("item1")) { // Items updated
            $action = Action::create(['action' => "Item details updated", 'table' => 'site_maintenance', 'table_id' => $main->id]);
            $item1->name = request("item1");
            $item1->save();
        }
        /*
        $order = 1;
        $current_items = $main->items->count();
        for ($i = 1; $i <= 25; $i ++) {
            $item = $main->item($i);
            if (request("item$i")) {
                if ($item) {
                    $item->name = request("item$i");
                    $item->order = $order;
                    $item->save();
                } else
                    SiteMaintenanceItem::create(['main_id' => $main->id, 'name' => request("item$i"), 'order' => $order, 'status' => 0]);
                $order ++;
            } elseif ($item)
                $item->delete();
        }

        if ($current_items != ($order - 1)) // Items updated
            $action = Action::create(['action' => "Items updated by " . Auth::user()->fullname, 'table' => 'site_maintenance', 'table_id' => $main->id]);
        */

        // Status Updated
        /*
        if (request('status') == 1) {  // Maintenance Request Accepted
            $main_request['step'] = 5;
            $action = Action::create(['action' => "Maintenance Request approved", 'table' => 'site_maintenance', 'table_id' => $main->id]);
        } elseif (request('status') == - 1)  // Maintenance Request Declined
            $action = Action::create(['action' => "Maintenance Request declined", 'table' => 'site_maintenance', 'table_id' => $main->id]);
        */

        //dd($main_request);
        Toastr::success("Updated Request");

        $main->update($main_request);

        // Email Assigned Supervisor
        if (Auth::user()->allowed2('sig.site.maintenance', $main)) {
            $main->emailAssigned($super);
            //$main = SiteMaintenance::findOrFail($id);
            //$email_super = (validEmail($super->email)) ? $super->email : '';
            //if ($email_super)
            //    Mail::to($email_super)->cc(['support@openhands.com.au'])->send(new \App\Mail\Site\SiteMaintenanceAssigned($main));
        }

        return (request('status') == 2) ? redirect('site/maintenance/' . $main->id . '/edit') : redirect('site/maintenance/' . $main->id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $main = SiteMaintenance::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.maintenance', $main))
            return view('errors/404');

        $rules = ['supervisor' => 'required', 'completed' => 'required', 'onhold_reason' => 'required_if:status,3'];
        $mesg = ['supervisor.required' => 'The supervisor field is required.', 'completed.required' => 'The prac completed field is required.', 'onhold_reason.required_if' => 'A reason is required to place request On Hold.'];
        request()->validate($rules, $mesg); // Validate

        $main_request = request()->all();
        //dd($main_request);

        // Verify prac completed date
        if (request('completed')) {
            if (preg_match("/(\d{2})\/(\d{2})\/(\d{4})$/", request('completed'), $matches)) {
                list($dd, $mm, $yyyy) = explode('/', request('completed'));
                if (checkdate($mm, $dd, $yyyy))
                    $main_request['completed'] = Carbon::createFromFormat('d/m/Y H:i:s', request('completed') . ' 00:00:00');
                else
                    return back()->withErrors(['completed' => "Invalid Prac Completed date. Required format dd/mm/yyyy"]);
            } else
                return back()->withErrors(['completed' => "Invalid Prac Completed date. Required format dd/mm/yyyy"]);
        }

        //dd($main_request);
        // Email if Super Assigned is updated
        if (request('super_id') && request('super_id') != $main->super_id) {
            $super = User::find($main_request['super_id']);
            $main->emailAssigned($super);
            $action = Action::create(['action' => "Assigned Task Owner updated to $super->name", 'table' => 'site_maintenance', 'table_id' => $main->id]);
        }

        // Email if Company Assigned is updated
        if (request('assigned_to') && request('assigned_to') != $main->assigned_to) {
            $company = Company::find($main_request['assigned_to']);
            if ($company && $company->primary_contact())
                $main->emailAssigned($company->primary_contact());
            $action = Action::create(['action' => "Company assigned to request updated to $company->name", 'table' => 'site_maintenance', 'table_id' => $main->id]);
        }

        // Add note if change of Status
        if (request('status') && $main->status != 3 && request('status') == 3)
            $action = Action::create(['action' => "Request has been placed On Hold for the following reason: \n".request('onhold_reason'), 'table' => 'site_maintenance', 'table_id' => $main->id]);
        if (request('status') && $main->status != 1 && request('status') == 1)
            $action = Action::create(['action' => "Request has been Re-Activated", 'table' => 'site_maintenance', 'table_id' => $main->id]);
        if (request('status') && $main->status != -1 && request('status') == -1)
            $action = Action::create(['action' => "Request has been Declined", 'table' => 'site_maintenance', 'table_id' => $main->id]);

        // Add note if change of Category
        if (request('category_id') && request('category_id') != $main->category_id) {
            $from = SiteMaintenanceCategory::find($main->category_id)->name;
            $to = SiteMaintenanceCategory::find(request('category_id'))->name;
            $action = Action::create(['action' => "Request category updated from $from to $to", 'table' => 'site_maintenance', 'table_id' => $main->id]);
        }


        //dd($main_request);
        $main->update($main_request);

        Toastr::success("Updated Request");

        return redirect('site/maintenance/' . $main->id);
    }


    /**
     * Update Status the specified resource in storage.
     */
    public function updateReport(Request $request, $id)
    {
        $main = SiteMaintenance::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.maintenance', $main))
            return view('errors/404');

        // Only Allow Ajax requests
        if ($request->ajax()) {
            $main_request = $request->all();

            // Determine if report being signed off
            $signoff = $request->get('signoff');
            if ($signoff == 'super') {
                $main_request['supervisor_sign_by'] = Auth::user()->id;
                $main_request['supervisor_sign_at'] = Carbon::now();

                // Close any outstanding ToDos for supervisors and Create one for Area Super / Con Mgr
                $main->closeToDo(Auth::user());
                if (!$main->manager_sign_by) {
                    $site = Site::findOrFail($main->site_id);
                    $main->createManagerSignOffToDo($site->areaSupervisors()->pluck('id')->toArray());
                }
                $action = Action::create(['action' => "Request has been signed off by Task Owner", 'table' => 'site_maintenance', 'table_id' => $main->id]);
            }
            if ($signoff == 'manager') {
                $main_request['manager_sign_by'] = Auth::user()->id;
                $main_request['manager_sign_at'] = Carbon::now();
                // Close any outstanding ToDos for Area Super / Con Mgr
                $main->closeToDo(Auth::user());

                // Update Site Status back to completed
                $main->site->status = 0;
                $main->site->save();

                $action = Action::create(['action' => "Request has been signed off by Construction Manager", 'table' => 'site_maintenance', 'table_id' => $main->id]);

                $email_list = [env('EMAIL_DEV')];
                if (\App::environment('prod'))
                    $email_list = $main->site->company->notificationsUsersEmailType('n.site.maintenance.completed');

                if ($email_list) Mail::to($email_list)->send(new \App\Mail\Site\SiteMaintenanceCompleted($main));
            }

            //dd($main_request);

            $main->update($main_request);

            // Determine if Report Signed Off and if so mark completed
            if ($main->supervisor_sign_by && $main->manager_sign_by) {
                $main->status = 0;
                $main->save();
            }


            Toastr::success("Updated Report");

            return $main;
        }

        return view('errors/404');
    }

    /**
     * Update Item the specified resource in storage.
     *
     */
    public function updateItem(Request $request, $id)
    {
        $item = SiteMaintenanceItem::findOrFail($id);
        $main = SiteMaintenance::findOrFail($item->main_id);
        // Check authorisation and throw 404 if not
        if (!(Auth::user()->allowed2('edit.site.maintenance', $main) || Auth::user()->id == $main->super_id))
            return view('errors/404');

        $item_request = $request->only(['status', 'done_by', 'sign_by']);
        //dd($item_request);

        // Update resolve date if just modified
        if (!request('status')) {
            $item->status = 0;
            $item->done_by = null;
            $item->done_at = null;
            $item->sign_by = null;
            $item->sign_at = null;
            $item->save();
            $action = Action::create(['action' => "Maintenance Item has been mark as NOT completed", 'table' => 'site_maintenance', 'table_id' => $main->id]);
        } else {
            // Item completed
            if ($item_request['status'] == 1 && $item->status != 1) {
                $item_request['done_by'] = Auth::user()->id;
                $item_request['done_at'] = Carbon::now()->toDateTimeString();
                $item_request['sign_by'] = Auth::user()->id;
                $item_request['sign_at'] = Carbon::now()->toDateTimeString();
                $action = Action::create(['action' => "Maintenance Item has been completed", 'table' => 'site_maintenance', 'table_id' => $main->id]);
            }
            /*
            // Item signed off
            if ($item_request['sign_by'] && !$item->sign_by) {
                $item_request['sign_by'] = Auth::user()->id;
                $item_request['sign_at'] = Carbon::now()->toDateTimeString();
            }
            // item marked incomplete
            if (!$item_request['sign_by'] && $item->sign_by) {
                $item_request['sign_by'] = null;
                $item_request['sign_at'] = null;
            }*/
            //dd($item_request);
            $item->update($item_request);
        }

        // Update modified timestamp on QA Doc
        $main = SiteMaintenance::findOrFail($item->main_id);
        $main->touch();

        Toastr::success("Updated record");

        return $item;
    }

    /**
     * Get Prac Completion date.
     */
    public function getPracCompletion()
    {
        $completed = SitePlanner::where('site_id', request('site_id'))->where('task_id', 265)->get()->last();
        if ($completed) {
            return $completed->to;
        }

        return '';
    }

    /**
     * Get Site Supervisor.
     */
    public function getSiteSupervisor()
    {
        $site = Site::find(request('site_id'));
        $supers = [$site->supervisorsSBC()];

        return ($site) ? $supers : '';
    }

    /**
     * Upload File + Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadAttachment(Request $request)
    {
        // Check authorisation and throw 404 if not
        //if (!(Auth::user()->allowed2('add.site.maintenance') || Auth::user()->allowed2('edit.site.maintenance', $main)))
        //    return json_encode("failed");

        //dd('here');
        //dd(request()->all());
        // Handle file upload
        $files = $request->file('multifile');
        foreach ($files as $file) {
            $path = "filebank/site/" . $request->get('site_id') . '/maintenance';
            $name = $request->get('site_id') . '-' . sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . strtolower($file->getClientOriginalExtension());

            // Ensure filename is unique by adding counter to similiar filenames
            $count = 1;
            while (file_exists(public_path("$path/$name")))
                $name = $request->get('site_id') . '-' . sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . $count ++ . '.' . strtolower($file->getClientOriginalExtension());
            $file->move($path, $name);

            $doc_request = $request->only('site_id');
            $doc_request['name'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $doc_request['company_id'] = Auth::user()->company_id;

            // Create SiteMaintenanceDoc
            $doc = SiteMaintenanceDoc::create($doc_request);
            $doc->main_id = $request->get('main_id');
            $doc->type = 'photo';
            $doc->attachment = $name;
            $doc->save();
        }


        return json_encode("success");
    }


    /**
     * Get QA Reports current user is authorised to manage + Process datatables ajax request.
     */
    public function getMaintenance()
    {
        $requests = Auth::user()->maintenanceRequests(request('status'));
        $request_ids = ($requests) ? Auth::user()->maintenanceRequests(request('status'))->pluck('id')->toArray() : [];

        $records = DB::table('site_maintenance AS m')
            ->select(['m.id', 'm.site_id', 'm.code', 'm.supervisor', 'm.assigned_to', 'm.super_id', 'm.completed', 'm.reported', 'm.warranty', 'm.goodwill', 'm.category_id', 'm.status', 'm.updated_at', 'm.created_at',
                DB::raw('DATE_FORMAT(m.reported, "%d/%m/%y") AS reported_date'),
                DB::raw('DATE_FORMAT(m.completed, "%d/%m/%y") AS completed_date'),
                DB::raw('DATE_FORMAT(m.updated_at, "%d/%m/%y") AS updated_date'),
                's.code as sitecode', 's.name as sitename'])
            ->join('sites AS s', 'm.site_id', '=', 's.id')
            ->whereIn('m.id', $request_ids)
            ->where('m.status', request('status'));

        //dd($records);
        $dt = Datatables::of($records)
            //->editColumn('id', '<div class="text-center"><a href="/site/maintenance/{{$id}}"><i class="fa fa-search"></i></a></div>')
            ->editColumn('id', function ($doc) {
                return "<div class='text-center'><a href='/site/maintenance/$doc->id'>M$doc->code</a></div>";
            })
            ->editColumn('site_id', function ($doc) {
                return $doc->sitecode;
            })
            ->editColumn('sitename', function ($doc) {
                return $doc->sitename;
            })
            ->editColumn('super_id', function ($doc) {
                $d = SiteMaintenance::find($doc->id);

                return ($d->super_id) ? $d->taskOwner->name : '-';
            })
            ->addColumn('last_updated', function ($doc) {
                $main = SiteMaintenance::find($doc->id);
                return ($main->lastAction()) ? $main->lastAction()->updated_at->format('d/m/Y') : $main->created_at->format('d/m/Y');
            })
            ->addColumn('completed', function ($doc) {
                $main = SiteMaintenance::find($doc->id);
                $total = $main->items()->count();
                $completed = $main->itemsCompleted()->count();
                $pending = '';
                if ($main->status != 0) {
                    if (Auth::user()->allowed2('edit.site.maintenance', $main)) {
                        if ($total == $completed && $total != 0) {
                            $label_type = ($main->supervisor_sign_by && $main->manager_sign_by) ? 'label-success' : 'label-warning';
                            if (!$main->supervisor_sign_by)
                                $pending = '<br><span class="badge badge-info badge-roundless pull-right">Pending Supervisor</span>';
                            elseif (!$main->manager_sign_by)
                                $pending = '<br><span class="badge badge-primary badge-roundless pull-right">Pending Manager</span>';
                        } else
                            $label_type = 'label-danger';

                        return '<span class="label pull-right ' . $label_type . '">' . $completed . ' / ' . $total . '</span>' . $pending;
                    }
                }

                return '<span class="label pull-right label-success">' . $completed . ' / ' . $total . '</span>';
            })
            ->addColumn('action', function ($doc) {
                $main = SiteMaintenance::find($doc->id);
                if (($doc->status && Auth::user()->allowed2('edit.site.maintenance', $main)) || (!$doc->status && Auth::user()->allowed2('sig.site.maintenance', $main)))
                    return '<a href="/site/maintenance/' . $doc->id . '" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>';

                return '<a href="/site/maintenance/' . $doc->id . '" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-search"></i> View</a>';

            })
            ->rawColumns(['id', 'name', 'updated_at', 'completed', 'action'])
            ->make(true);

        return $dt;
    }

    /**
     * Display the specified resource.
     */
    public function exportQA()
    {
        return view('site/export/qa');
    }


    public function getItems(Request $request, $id)
    {
        //if ($request->ajax()) {

        $main = SiteMaintenance::findOrFail($id);

        $items = [];
        $users = [];
        $companies = [];
        foreach ($main->items as $item) {
            $array = [];
            $array['id'] = $item->id;
            $array['order'] = $item->order;
            $array['name'] = $item->name;
            $array['super'] = $item->super;

            // Task Info
            //$array['task_id'] = $item->task_id;
            //$task = Task::find($item->task_id);
            //$array['task_name'] = $task->name;
            //$array['task_code'] = $task->code;


            // Done By
            $array['done_at'] = '';
            $array['done_by'] = '';
            $array['done_by_name'] = '';
            $array['done_by_company'] = '';
            $array['done_by_licence'] = '';
            if ($item->done_by) {
                // User Info - Array of unique users (store previous users to speed up)
                if (isset($users[$item->done_by])) {
                    $user_rec = $users[$item->done_by];
                } else {
                    $user = User::find($item->done_by);
                    $users[$item->done_by] = (object) ['id' => $user->id, 'full_name' => $user->full_name, 'company_name' => $user->company->name_alias];
                    $user_rec = $users[$item->done_by];
                }

                $array['done_at'] = $item->done_at->format('Y-m-d');
                $array['done_by'] = $user_rec->id;
                $array['done_by_name'] = $user_rec->full_name;
                $array['done_by_company'] = $user_rec->company_name;
            }

            // Signed By
            $array['sign_at'] = '';
            $array['sign_by'] = '';
            $array['sign_by_name'] = '';
            if ($item->sign_by) {
                // User Info - Array of unique users (store previous users to speed up)
                if (isset($users[$item->sign_by])) {
                    $user = $users[$item->sign_by];
                } else {
                    $user = User::find($item->sign_by);
                    $users[$item->sign_by] = (object) ['id' => $user->id, 'full_name' => $user->full_name];
                }

                $array['sign_at'] = $item->sign_at->format('Y-m-d');
                $array['sign_by'] = $user->id;
                $array['sign_by_name'] = $user->full_name;
            }


            $array['status'] = $item->status;
            $items[] = $array;
        };


        $actions = [];
        $actions[] = ['value' => '', 'text' => 'Select Action'];
        $actions[] = ['value' => '1', 'text' => 'Completed'];
        //$actions[] = ['value' => '-1', 'text' => 'Mark N/A'];
        $actions2[] = ['value' => '', 'text' => 'Select Action'];
        $actions2[] = ['value' => '0', 'text' => 'Incomplete'];
        $actions2[] = ['value' => '1', 'text' => 'Sign Off'];

        $json = [];
        $json[] = $items;
        $json[] = $actions;
        $json[] = $actions2;

        return $json;
        //}
    }
}
