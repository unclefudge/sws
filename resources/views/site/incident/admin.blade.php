@inject('ozstates', 'App\Http\Utilities\OzStates')
@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        @if (Auth::user()->hasAnyPermissionType('site.incident'))
            <li><a href="/site/incident">Site Incidents</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Incident Report</span></li>
    </ul>
@stop

@section('content')
    {{-- BEGIN PAGE CONTENT INNER --}}
    <div class="page-content-inner">

        @include('site/incident/_header')
        <?php
        $qConditions = App\Models\Misc\FormQuestion::find(113);
        $qConFactorDefences = App\Models\Misc\FormQuestion::find(125);
        $qConFactorITactions = App\Models\Misc\FormQuestion::find(148);
        $qConFactorWorkplace = App\Models\Misc\FormQuestion::find(167);
        $qConFactorHuman = App\Models\Misc\FormQuestion::find(192);
        $qRootCause = App\Models\Misc\FormQuestion::find(219);
        ?>

        <div class="row">
            <div class="col-lg-6 col-xs-12 col-sm-12">
                {{-- Details --}}
                @if (Auth::user()->allowed2('view.site.incident', $incident))
                    @include('site/incident/_show-details')
                    @include('site/incident/_edit-details')
                @endif

                {{-- Regulator --}}
                @if (Auth::user()->allowed2('view.site.incident', $incident))
                    @include('site/incident/_show-regulator')
                    @include('site/incident/_edit-regulator')
                @endif
            </div>

            <div class="col-lg-6 col-xs-12 col-sm-12">
                {{-- Witness Statements --}}
                @if (Auth::user()->allowed2('view.site.incident', $incident))
                    @include('site/incident/_show-witness')
                @endif

                {{-- Conversations --}}
                @if (Auth::user()->allowed2('view.site.incident', $incident))
                    @include('site/incident/_show-conversation')
                @endif
            </div>

        </div>
    </div>

    <div>
        <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
            {!! $incident->displayUpdatedBy() !!}
        </div>
    </div>

@stop

@section('page-level-plugins-head')
    <link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-styles-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        // On Change Notifiable
        $("#notifiable").change(function () {
            $("#edit_regulator").hide();
            if ($("#notifiable").val() == '1') {
                $("#show_regulator").show();
            } else
                $("#show_regulator").hide();
        });
    });

    function editForm(name) {
        $('#show_' + name).hide();
        $('#edit_' + name).show();
        $('#add_' + name).hide();
    }

    function cancelForm(e, name) {
        e.preventDefault();
        $('#show_' + name).show();
        $('#edit_' + name).hide();
        $('#add_' + name).hide();
    }

    function addForm(name) {
        $('#show_' + name).hide();
        $('#edit_' + name).hide();
        $('#add_' + name).show();
    }

            @if (count($errors) > 0)
    var errors = {!! $errors !!};
    if (errors.FORM == 'details' || errors.FORM == 'regulator') {
        $('#show_' + errors.FORM).hide();
        $('#edit_' + errors.FORM).show();
    }

    console.log(errors)
    @endif

    // Force datepicker to not be able to select dates after today
    $('.bs-datetime').datetimepicker({
        endDate: new Date(),
        format: 'dd/mm/yyyy hh:ii',
    });


</script>
@stop