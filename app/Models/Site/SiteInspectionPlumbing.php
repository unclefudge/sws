<?php

namespace App\Models\Site;

use Mail;
use App\User;
use App\Models\Misc\Action;
use App\Models\Comms\Todo;
use App\Models\Site\SiteInspectionDoc;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;

class SiteInspectionPlumbing extends Model {

    protected $table = 'site_inspection_plumbing';
    protected $fillable = [
        'site_id', 'client_name', 'client_address', 'client_contacted', 'info', 'assigned_to', 'assigned_at', 'inspected_by', 'inspected_at', 'inspected_name', 'inspected_lic',
        'pressure', 'pressure_reduction', 'pressure_cost', 'pressure_notes', 'hammer', 'hammer_notes', 'hotwater_type', 'hotwater_lowered',
        'fuel_type', 'gas_position', 'gas_pipes', 'gas_lines', 'gas_notes', 'existing', 'existing_notes',
        'sewer_cost', 'sewer_allowance', 'sewer_extra', 'sewer_notes',
        'stormwater_cost', 'stormwater_allowance', 'stormwater_extra', 'stormwater_notes', 'stormwater_detention_type', 'stormwater_detention_notes',
        'notes', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'
    ];

    protected $dates = ['client_contacted', 'inspected_at', 'assigned_at'];

    /**
     * A SiteInspectionPlumbing belongs to a site
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site()
    {
        return $this->belongsTo('App\Models\Site\Site');
    }

    /**
     * A SiteInspectionPlumbing assigned to a company
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function assignedTo()
    {
        return $this->belongsTo('App\Models\Company\Company', 'assigned_to');
    }

    /**
     * A SiteInspectionElectrical has many Docs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function docs()
    {
        return SiteInspectionDoc::where('inspect_id', $this->id)->where('table', 'plumbing')->get();
    }

    /**
     * A SiteInspectionPlumbing belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Create ToDoo for Plumbing Report and assign to given user(s)
     */
    public function createAssignedToDo($user_list)
    {
        $todo_request = [
            'type'       => 'inspection_plumbing',
            'type_id'    => $this->id,
            'name'       => 'Plumbing Inspection Report - ' . ' (' . $this->site->name . ')',
            'info'       => 'Please complete the inspection report',
            'priority'   => '1',
            'due_at'     => nextWorkDate(Carbon::today(), '+', 2)->toDateTimeString(),
            'company_id' => '3',
        ];

        // Create ToDoo and assign to Site Supervisors
        $todo = Todo::create($todo_request);
        $todo->assignUsers($user_list);
        $todo->emailToDo();
    }

    /**
     * Create ToDoo for Plumbing Report and assign to given user(s)
     */
    public function createContructionToDo($user_list)
    {
        // Create ToDoo for Construction Manager to assign to company
        $todo_request = [
            'type'       => 'inspection_plumbing',
            'type_id'    => $this->id,
            'name'       => 'Plumbing Inspection Report Created - ' . ' (' . $this->site->name . ')',
            'info'       => 'Please review inspection and assign to a company',
            'due_at'     => nextWorkDate(Carbon::today(), '+', 14)->toDateTimeString(),
            'company_id' => $this->site->owned_by->id,
        ];

        // Create ToDoo and assign to Construction Manager
        $todo = Todo::create($todo_request);
        $todo->assignUsers($user_list);
    }

    /**
     * Close any outstanding ToDoo for this QA
     */
    public function closeToDo()
    {
        $todos = Todo::where('type', 'inspection_plumbing')->where('type_id', $this->id)->where('status', '1')->get();
        foreach ($todos as $todo) {
            $todo->status = 0;
            $todo->done_at = Carbon::now();
            $todo->done_by = Auth::user()->id;
            $todo->save();
        }
    }

    /**
     * Get the owner of record   (getter)
     *
     * @return string;
     */
    public function getOwnedByAttribute()
    {
        return $this->site->company;
    }

    /**
     * Display records last update_by + date
     *
     * @return string
     */
    public function displayUpdatedBy()
    {
        $user = User::findOrFail($this->updated_by);

        return '<span style="font-weight: 400">Last modified: </span>' . $this->updated_at->diffForHumans() . ' &nbsp; ' .
        '<span style="font-weight: 400">By:</span> ' . $user->fullname;
    }

    /**
     * The "booting" method of the model.
     *
     * Overrides parent function
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        if (Auth::check()) {
            // create a event to happen on creating
            static::creating(function ($table) {
                $table->created_by = Auth::user()->id;
                $table->updated_by = Auth::user()->id;
            });

            // create a event to happen on updating
            static::updating(function ($table) {
                $table->updated_by = Auth::user()->id;
            });
        }
    }
}