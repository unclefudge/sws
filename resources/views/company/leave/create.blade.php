@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-industry"></i> Company Leave</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><a href="/company">Companies</a><i class="fa fa-circle"></i></li>
        <li><a href="/company/leave">Company leave</a><i class="fa fa-circle"></i></li>
        <li><span>Create new leave</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-pencil "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Create New Company Leave</span>
                            <span class="caption-helper"></span>
                        </div>
                        <div class="actions">
                            <a href="" class="btn btn-circle btn-icon-only btn-default collapse"> </a>
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('company_leave', ['action' => 'Company\CompanyLeaveController@store', 'class' => 'horizontal-form']) !!}

                        @include('form-error')

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group {!! fieldHasError('company_id', $errors) !!}">
                                        {!! Form::label('company_id', 'Company', ['class' => 'control-label']) !!}
                                        {!! Form::select('company_id', Auth::user()->company->companiesSelect('prompt'),
                                         null, ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('company_id', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group {!! fieldHasError('from', $errors) !!}">
                                        {!! Form::label('from', 'Leave From', ['class' => 'control-label']) !!}
                                        <div class="input-group date date-picker input-daterange" data-date-format="dd/mm/yyyy">
                                            {!! Form::text('from', null, ['class' => 'form-control', 'readonly', 'style' => 'background:#FFF']) !!}
                                            <span class="input-group-addon"> to </span>
                                            {!! Form::text('to', null, ['class' => 'form-control', 'readonly', 'style' => 'background:#FFF']) !!}
                                        </div>
                                        {!! fieldErrorMessage('start_date', $errors) !!}
                                    </div>
                                </div>
                            </div>
                            <h3 class="form-section"></h3>
                            <!-- Notes -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group {!! fieldHasError('notes', $errors) !!}">
                                        {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                                        {!! Form::textarea('notes', null, ['rows' => '2', 'class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('notes', $errors) !!}
                                        <span class="help-block"> For internal use only </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions right">
                                <a href="/company/leave" class="btn default"> Back</a>
                                <button type="submit" class="btn green">Save</button>
                            </div>
                        </div> <!--/form-body-->
                        {!! Form::close() !!}
                                <!-- END FORM-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stop <!-- END Content -->


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
@stop

