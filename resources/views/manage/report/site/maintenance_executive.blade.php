@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('manage.report'))
            <li><a href="/manage/report">Management Reports</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Maintenance Executive Summary</span></li>
    </ul>
    @stop

    @section('content')

            <!-- BEGIN PAGE CONTENT INNER -->
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Maintenance Executive Summary</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-4">Date Range (90 days)</div>
                            <div class="col-md-4">{{ $from->format('d M') }} - {{ $to->format('d M Y') }}</div>
                            <div class="col-md-2">Total Requests</div>
                            <div class="col-md-2">{{ ($mains->count() + $mains_old->count()) }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">Average days for allocating Requests
                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                   data-content="Calculated 'Working Days' from time request is reported to assigned to Supervisor"
                                   data-original-title="Average days for allocating Requests"> <i class="fa fa-question-circle font-grey-silver"></i> </a></div>
                            <div class="col-md-4">{{ $avg_allocated }}</div>
                            <div class="col-md-2">New Requests</div>
                            <div class="col-md-2">{{ $mains_created->count() }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">Average days for client contacted
                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                   data-content="Calculated 'Working Days' from time request is reported to the date 'first contact' is made with the client"
                                   data-original-title="Average days for client contacted"> <i class="fa fa-question-circle font-grey-silver"></i> </a></div>
                            <div class="col-md-4">{{ $avg_contacted }}</div>
                            <div class="col-md-2">Unique Sites</div>
                            <div class="col-md-2">{{ ($mains->groupBy('site_id')->count() + $mains_old->groupBy('site_id')->count()) }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">Average days from appointment to completion
                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                   data-content="Calculated 'Working Days' to complete from either Client Appointment date or the reported date in cases of no appointment date"
                                   data-original-title="Average days appointment to completion"> <i class="fa fa-question-circle font-grey-silver"></i> </a></div>
                            <div class="col-md-4">{{ $avg_appoint }}</div>
                            <div class="col-md-2"></div>
                            <div class="col-md-2"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">Average days for completing Requests
                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                   data-content="Calculated 'Working Days' from time request is reported to either a) Completed b) placed On Hold c) end of date range"
                                   data-original-title="Average days for completing Requests"> <i class="fa fa-question-circle font-grey-silver"></i> </a></div>
                            <div class="col-md-4">{{ $avg_completed }}</div>
                            <div class="col-md-2"></div>
                            <div class="col-md-2"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12"><span class="font-red">Above stats are calculated from requests created after 1st May and exclude {{ $excluded }} earlier requests.</span> </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <b>Categories Summary</b>
                                @foreach ($cats as $name => $count)
                                    <div class="row">
                                        <div class="col-xs-1">{{ $count }}</div>
                                        <div class="col-xs-11">{!! $name !!}</div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-6">
                                <div class="visible-sm visible-xs"><br></div>
                                <div class="row">
                                    <div class="col-xs-1"><b>#</b></div>
                                    <div class="col-xs-5"><b>Supervisor</b></div>
                                    <div class="col-xs-2"><b>Active</b></div>
                                    <div class="col-xs-2"><b>Completed</b></div>
                                    <div class="col-xs-2"><b>On Hold</b></div>
                                </div>
                                @foreach ($supers as $name => $count)
                                    <div class="row">
                                        <div class="col-xs-1">{!! ($count[0] + $count[1] + $count[2]) !!}</div>
                                        <div class="col-xs-5">{{ $name }}</div>
                                        <div class="col-xs-2">{!! $count[0] !!}</div>
                                        <div class="col-xs-2">{!! $count[1] !!}</div>
                                        <div class="col-xs-2">{!! $count[2] !!}</div>
                                    </div>
                                @endforeach
                                <hr style="padding: 2px; margin: 2px 0px">
                                <div class="row">
                                    <div class="col-xs-6">&nbsp;</div>
                                    <div class="col-xs-2">{{ ($mains->where('status', 1)->count() + $mains_old->where('status', 1)->count()) }}</div>
                                    <div class="col-xs-2">{{ ($mains->where('status', 0)->count() + $mains_old->where('status', 0)->count()) }}</div>
                                    <div class="col-xs-2">{{ ($mains->where('status', 3)->count() + $mains_old->where('status', 3)->count()) }}</div>
                                </div>

                            </div>
                        </div>
                        <hr>
                        <h2>Open Requests Older than 90 Days &nbsp;
                            <small style="color: #999"> (#{{ $mains_old->count() }})</small>
                        </h2>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th>Site</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Supervisor</th>
                                <th width="10%">Reported Date</th>
                                <th width="10%">Allocated Date</th>
                                <th width="10%">Completed</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($mains_old as $main)
                                <tr>
                                    <td>
                                        <div class="text-center"><a href="/site/maintenance/{{ $main->id }}">M{{ $main->code }}</a></div>
                                    </td>
                                    <td>{{ $main->site->code }}</td>
                                    <td>{{ $main->site->name }}</td>
                                    <td>{{ ($main->category_id) ? \App\Models\Site\SiteMaintenanceCategory::find($main->category_id)->name : '-' }}</td>
                                    <td>{{ ($main->super_id) ? $main->taskOwner->name : 'Unassigned' }}</td>
                                    <td>{{ $main->reported->format('d/m/Y') }}</td>
                                    <td>{{ ($main->assigned_at) ? $main->assigned_at->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if ($main->status == 0)
                                            {{  $main->updated_at->format('d/m/Y') }}
                                        @else
                                            {{ ($main->status && $main->status == 1) ? 'Active' : 'On Hold'  }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <h2>Requests Updated in Last 90 Days &nbsp;
                            <small style="color: #999"> (#{{ $mains->count() }})</small>
                        </h2>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th>Site</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Task Owner</th>
                                <th width="10%">Reported Date</th>
                                <th width="10%">Allocated Date</th>
                                <th width="10%">Completed</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($mains as $main)
                                <tr>
                                    <td>
                                        <div class="text-center"><a href="/site/maintenance/{{ $main->id }}">M{{ $main->code }}</a></div>
                                    </td>
                                    <td>{{ $main->site->code }}</td>
                                    <td>{{ $main->site->name }}</td>
                                    <td>{{ ($main->category_id) ? \App\Models\Site\SiteMaintenanceCategory::find($main->category_id)->name : '-' }}</td>
                                    <td>{{ ($main->super_id) ? $main->taskOwner->name : 'Unassigned' }}</td>
                                    <td>{{ $main->reported->format('d/m/Y') }}</td>
                                    <td>{{ ($main->assigned_at) ? $main->assigned_at->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if ($main->status == 0)
                                            {{  $main->updated_at->format('d/m/Y') }}
                                        @else
                                            {{ ($main->status && $main->status == 1) ? 'Active' : 'On Hold'  }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop


@section('page-level-plugins-head')
@stop

@section('page-level-plugins')
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
@stop