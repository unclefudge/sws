@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('manage.report'))
            <li><a href="/manage/report">Management Reports</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Equipment List</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        {!! Form::model('EquipmentPdf', ['action' => 'Site\Planner\SitePlannerExportController@attendancePDF', 'class' => 'horizontal-form']) !!}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Equipment List</span>
                        </div>
                        <div class="actions">
                            <a href="/manage/report/equipment/report" class="btn btn-circle btn-outline btn-sm green" id="view_pdf"> View PDF</a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="portlet-body">

                            <div id="build-wrap"></div>
                            @foreach ($equipment as $equip)
                                <div class="row">
                                    <div class="col-md-12"><b>{{ $equip->name }} ({{ $equip->total }})</b></div>
                                </div>
                                @foreach ($equip->locations() as $location)
                                    @if ($location->equipment($equip->id)->qty)
                                        <div class="row">
                                            <div class="col-xs-1 text-right">{{ $location->equipment($equip->id)->qty }}</div>
                                            <div class="col-xs-11">{{ $location->name2 }}</div>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>

                        <div class="form-actions right">
                            <a href="/manage/report" class="btn default"> Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- loading Spinner -->
    <div style="background-color: #FFF; padding: 20px; display: none" id="spinner">
        <div class="loadSpinnerOverlay">
            <div class="loadSpinner"><i class="fa fa-spinner fa-pulse fa-2x fa-fw margin-bottom"></i> Loading...</div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>

<script type="text/javascript">
</script>
@stop