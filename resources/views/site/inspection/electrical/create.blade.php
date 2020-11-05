@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->company->subscription)
            <li><a href="/site/inspection/electrical">Site Inspection Electrical</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Create Report</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-green-haze bold uppercase">Create Site Inspection Electrical Report</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('SiteInspectionElectrical', ['action' => 'Site\SiteInspectionElectricalController@store', 'class' => 'horizontal-form']) !!}

                        @include('form-error')
                        <div class="form-body">
                            <div class="row">
                                {{-- Site --}}
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('site_id', $errors) !!}">
                                        {!! Form::label('site_id', 'Site', ['class' => 'control-label']) !!}
                                        <select id="site_id" name="site_id" class="form-control select2" style="width:100%">
                                            {!! Auth::user()->authSitesSelect2Options('view.site', old('site_id')) !!}
                                        </select>
                                        {!! fieldErrorMessage('site_id', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-1"></div>
                                {{-- Assigned To Company --}}
                                <div class="col-md-5">
                                    <div class="form-group {!! fieldHasError('assigned_to', $errors) !!}" style="{{ fieldHasError('assigned_to', $errors) ? '' : 'display:show' }}" id="company-div">
                                        {!! Form::label('assigned_to', 'Assigned to company', ['class' => 'control-label']) !!}
                                        <select id="assigned_to" name="assigned_to" class="form-control bs-select" style="width:100%">
                                            <option value="">Select company</option>
                                            @foreach (Auth::user()->company->reportsTo()->companies('1')->sortBy('name') as $company)
                                                @if (in_array('4', $company->tradesSkilledIn->pluck('id')->toArray()))
                                                    <option value="{{ $company->id }}" {{ (old('assigned_to') && old('assigned_to') == $company->id) ? 'selected' : '' }}>{{ $company->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        {!! fieldErrorMessage('assigned_to', $errors) !!}
                                    </div>
                                </div>
                            </div>

                            <h4 class="font-green-haze">Client details</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group {!! fieldHasError('client_name', $errors) !!}">
                                        {!! Form::label('client_name', 'Name', ['class' => 'control-label']) !!}
                                        {!! Form::text('client_name', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('client_name', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group {!! fieldHasError('client_address', $errors) !!}">
                                        {!! Form::label('client_address', 'Address', ['class' => 'control-label']) !!}
                                        {!! Form::text('client_address', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('client_address', $errors) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions right">
                                <a href="/site/inspection/electrical" class="btn default"> Back</a>
                                <button type="submit" class="btn green"> Save</button>
                            </div>
                        </div>
                        {!! Form::close() !!} <!-- END FORM-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stop <!-- END Content -->


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>

    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        /* Select2 */
        $("#site_id").select2({placeholder: "Select Site"});
        $("#assigned_to").select2({placeholder: "Select Company"});

        updateFields();

        // On Change Site ID
        $("#site_id").change(function () {
            updateFields();
        });

        function updateFields() {
            var site_id = $("#site_id").select2("val");

            if (site_id != '') {
                $.ajax({
                    url: '/site/data/details/' + site_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        var address = '';
                        address = data.address;
                        if (data.address != '') address = address + ', ';
                        if (data.suburb != '') address = address + data.suburb + ', ';
                        if (data.state != '') address = address + data.state + ' ';
                        if (data.postcode != '') address = address + data.postcode + ' ';

                        $("#client_address").val(address);
                        $("#client_name").val(data.name);
                        //console.log(address);
                    },
                })
            }
        }

    });

    // Force datepicker to not be able to select dates after today
    //$('.bs-datetime').datetimepicker({
    //    endDate: new Date()
    //});
</script>
@stop


