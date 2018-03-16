@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-medkit"></i> Site Accidents</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Site Accidents</span></li>
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
                            <span class="caption-subject bold uppercase font-green-haze"> Site Accidents</span>
                        </div>
                        <div class="actions">
                            @if(Auth::user()->allowed2('add.site.accident'))
                                <a class="btn btn-circle green btn-outline btn-sm" href="/site/accident/create" data-original-title="Add">Add</a>
                            @endif
                            <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"></a>
                        </div>
                    </div>
                    <div class="row">
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
                                <th> Name</th>
                                <th> Supervisor</th>
                                <th> Location</th>
                                <th> Nature</th>
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
            'url': '{!! url('site/accident/dt/accidents') !!}',
            'type': 'GET',
            'data': function (d) {
                d.status = $('#status').val();
            }
        },
        columns: [
            {data: 'view', name: 'view', orderable: false, searchable: false},
            {data: 'id', name: 'site_accidents.id', orderable: false, searchable: false},
            {data: 'nicedate', name: 'site_accidents.date'},
            {data: 'nicedate2', name: 'site_accidents.resolved_at', visible: false,},
            {data: 'sitename', name: 'sites.name'},
            {data: 'name', name: 'site_accidents.name'},
            {data: 'supervisor', name: 'supervisor', orderable: false, searchable: false},
            {data: 'location', name: 'site_accidents.location', orderable: false},
            {data: 'nature', name: 'site_accidents.nature', orderable: false},
        ],
        order: [
            [2, "desc"]
        ]
    });

    $('select#status').change(function () {
        if ($('#status').val() == 0)
            table_list.column('3').visible(true);
        else
            table_list.column('3').visible(false);
        table_list.ajax.reload();
    });
</script>
@stop