@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-exclamation-triangle"></i> Hazard Register</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Hazard Register</span></li>
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
                            <span class="caption-subject bold uppercase font-green-haze"> Hazard Register</span>
                        </div>
                        <div class="actions">
                            @if (Auth::user()->hasPermission2('add.site.hazard'))
                                <a class="btn btn-circle green btn-outline btn-sm" href="/site/hazard/create" data-original-title="Add">Add</a>
                            @endif
                            <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"></a>
                        </div>
                    </div>

                    <div class="row">
                        @if (Auth::user()->company->subscription && Auth::user()->company->parent_company)
                            <div class="col-md-5">
                                <div class="form-group">
                                    {!! Form::select('site_group', ['0' => 'All Sites', Auth::user()->company_id => Auth::user()->company->name.' sites',
                                    Auth::user()->company->parent_company => Auth::user()->company->reportsTo()->name.' sites'], null, ['class' => 'form-control bs-select', 'id' => 'site_group']) !!}
                                </div>
                            </div>
                        @else
                            {!! Form::hidden('site_group', '') !!}
                        @endif

                        <div class="col-md-2 pull-right">
                            <div class="form-group">
                                <select name="status" id="status" class="form-control bs-select">
                                    <option value="1" selected>Open</option>
                                    <option value="0">Resolved</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th width="5%"> ID</th>
                                <th width="10%"> Date</th>
                                <th width="10%"> Resolved</th>
                                <th> Site</th>
                                <th> Initiated By</th>
                                <th> Supervisor</th>
                                <th> Safety Concern</th>
                                <th width="5%"> Req</th>
                                <th width="2%"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop

@section('page-level-plugins-head')
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}

<script type="text/javascript">

    var status = $('#status').val();

    var table_list = $('#table_list').DataTable({
        pageLength: 100,
        processing: true,
        serverSide: true,
        ajax: {
            'url': '{!! url('/site/hazard/dt/hazards') !!}',
            'type': 'GET',
            'data': function (d) {
                d.site_group = $('#site_group').val();
                d.status = $('#status').val();
            }
        },
        columns: [
            {data: 'view', name: 'view', orderable: false, searchable: false},
            {data: 'id', name: 'site_hazards.id', orderable: false, searchable: false},
            {data: 'nicedate', name: 'site_hazards.created_at'},
            {data: 'nicedate2', name: 'site_hazards.resolved_at', visible: false,},
            {data: 'name', name: 'sites.name'},
            {data: 'fullname', name: 'fullname', orderable: false, searchable: false},
            {data: 'supervisor', name: 'supervisor', orderable: false, searchable: false},
            {data: 'reason', name: 'site_hazards.reason'},
            {data: 'action_required', name: 'site_hazards.action_required'},
            {data: 'attachment', name: 'site_hazards.attachment', orderable: false, searchable: false},
        ],
        order: [
            [2, "desc"]
        ]
    });

    $('select#site_group').change(function () {table_list.ajax.reload();});

    $('select#status').change(function () {
        if ($('#status').val() == 0)
            table_list.column('3').visible(true);
        else
            table_list.column('3').visible(false);
        table_list.ajax.reload();
    });
</script>

<script src="/js/libs/html5lightbox/html5lightbox.js" type="text/javascript"></script>
@stop