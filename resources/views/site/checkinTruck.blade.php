@extends('layout-basic')

@section('pagetitle')
    @if (Session::has('siteID') && $worksite->isUserOnsite(Auth::user()->id))
        <a href="/"><img src="/img/logo2-sws.png" alt="logo" class="logo-default" style="margin-top:15px"></a>
    @else
        <img src="/img/logo2-sws.png" alt="logo" class="logo-default" style="margin-top:15px">
    @endif
    <div class="pull-right" style="padding: 20px;"><a href="/logout">logout</a></div>
@stop

@section('breadcrumbs')
    @if (Session::has('siteID') && $worksite->isUserOnsite(Auth::user()->id))
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
            <li><span>Check-in</span></li>
        </ul>
    @endif
@stop

@section('content')
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-sign-in "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Site Checkin</span><br>
                            <span class="caption-helper">You must check into all sites you attend.</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <h2>{{ $worksite->name }}
                            <small>(Site: {{ $worksite->code }})</small>
                        </h2>
                        <p>{{ $worksite->address }}, {{ $worksite->suburb }}</p>
                        <hr>

                        <!-- BEGIN FORM-->
                        {!! Form::model('site_attenance', ['action' => ['Site\SiteCheckinController@processCheckin', $worksite->id], 'files' => true]) !!}

                        @include('form-error')

                        <p>Please answer the following questions.</p>
                        <div class="form-body">
                            {{--}}
                            <div class="note note-success">
                                <h4><b>COVID - Safety Requirements</b></h4>
                                <hr style="color: #000">
                                <div class="row">
                                    <div class="col-sm-2 col-xs-4 text-center">
                                        <div class="form-group">
                                            {!! Form::checkbox('question20', '1', false,
                                             ['class' => 'make-switch', 'data-size' => 'small',
                                             'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                             'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-10 col-xs-8">
                                        I have <b>signed in</b> to the NSW service Covid safe check in
                                    </div>
                                </div>
                                <div class="row visible-xs">&nbsp;</div>
                                <div class="row">
                                    <div class="col-sm-2 col-xs-4 text-center">
                                        <div class="form-group">
                                            {!! Form::checkbox('question21', '1', false,
                                             ['class' => 'make-switch', 'data-size' => 'small',
                                             'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                             'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-10 col-xs-8">
                                        I will <b>wear a mask</b> as required and <b>observe all other Gov directives</b>.
                                    </div>
                                </div>
                                <div class="row visible-xs">&nbsp;</div>
                                <div class="row">
                                    <div class="col-sm-2 col-xs-4 text-center">
                                        <div class="form-group">
                                            {!! Form::checkbox('question22', '1', false,
                                             ['class' => 'make-switch', 'data-size' => 'small',
                                             'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                             'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-10 col-xs-8">
                                        I understand the current NSW Health orders and <b>comply with its requirements</b> in relation to vaccinations and or covid testing.
                                    </div>
                                </div>
                            </div> --}}

                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('question2', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I declare I am <b>fit for work</b> and am <b>not under the influence of alcohol, drugs or prescription medication</b> that may affect my capacity to operate a vehicle
                                </div>
                            </div>
                            <div class="row visible-xs">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('question3', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I declare I am <b>not affected by any pre-existing medical condition</b> that may be aggravated by my work duties <b>OR</b> I have <b>declared any pre-existing
                                        medical conditions to my employer</b> and I will work in accordance with the arranged suitable duties
                                </div>
                            </div>
                            <div class="row visible-xs">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('question14', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I declare I hold a <b>current and valid driver’s licence</b> to operate the vehicle
                                </div>
                            </div>
                            <div class="row visible-xs">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('question15', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I will abide by the <b>road rules and be a courteous & responsible driver</b>
                                </div>
                            </div>
                            <div class="row visible-xs">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('question16', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I will report any <b>damage and defects</b> to the vehicle
                                </div>
                            </div>
                            <div class="row visible-xs">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('question17', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I will notify Cape Cod of any <b>maintenance requirements</b> (including servicing requirements)
                                </div>
                            </div>
                            <div class="row visible-xs">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('safe_site', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger', 'id'=>'safe_site']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I have <b>conducted my own assessment</b> of the vehicle’s condition and believe it to be <b>safe to use</b> and there are no new defects or damage to report
                                </div>
                            </div>

                            <!-- Unsafe Site Fields -->
                            <div id="unsafe-site">
                                <hr>
                                <h4 class="font-green-haze">Hazard Details</h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group {!! fieldHasError('location', $errors) !!}">
                                            {!! Form::label('location', 'Location of hazard (eg. bathroom, first floor addition, kitchen, backyard)', ['class' => 'control-label']) !!}
                                            {!! Form::text('location', null, ['class' => 'form-control']) !!}
                                            {!! fieldErrorMessage('location', $errors) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group {!! fieldHasError('rating', $errors) !!}">
                                            {!! Form::label('rating', 'Risk Rating', ['class' => 'control-label']) !!}
                                            {!! Form::select('rating', ['' => 'Select rating', '1' => "Low", '2' => 'Medium', '3' => 'High', '4' => 'Extreme'], null, ['class' => 'form-control bs-select']) !!}
                                            {!! fieldErrorMessage('rating', $errors) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group {!! fieldHasError('reason', $errors) !!}">
                                            {!! Form::label('reason', 'What is the hazard / safety issue?', ['class' => 'control-label']) !!}
                                            {!! Form::textarea('reason', null, ['rows' => '3', 'class' => 'form-control']) !!}
                                            {!! fieldErrorMessage('reason', $errors) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group {!! fieldHasError('action', $errors) !!}">
                                            {!! Form::label('action', 'What action/s (if any) have you taken to resolve the issue?', ['class' => 'control-label']) !!}
                                            {!! Form::textarea('action', null, ['rows' => '3', 'class' => 'form-control']) !!}
                                            {!! fieldErrorMessage('action', $errors) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-preview fileinput-exists thumbnail"
                                                     style="max-width: 200px; max-height: 150px;"></div>
                                                <div>
                                                        <span class="btn default btn-file">
                                                            <span class="fileinput-new"> Upload Photo/Video of issue</span>
                                                            <span class="fileinput-exists"> Change </span>
                                                            <input type="file" name="media">
                                                        </span>
                                                    <a href="javascript:;" class="btn default fileinput-exists"
                                                       data-dismiss="fileinput">Remove </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--
                                <div class="row visible-xs">
                                    <div class="form-group">
                                        <label for="media">File input</label>
                                        <input type="file" name="media2" id="media2">
                                        <p class="help-block"> some help text here. </p>
                                    </div>
                                </div>
                                -->
                                <div class="row">
                                    <div class="col-sm-2 col-xs-4 text-center">
                                        <div class="form-group">
                                            {!! Form::checkbox('action_required', '1', null,
                                             ['class' => 'make-switch', 'data-size' => 'small',
                                             'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                             'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-10 col-xs-8">
                                        Does {{ $worksite->company->name }} need to take any action?
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn green" name="checkinTruck" value="true">Submit</button>
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
    <link href="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        //$('#safe_site').bootstrapSwitch('state', false);
        //var state = $('#safe_site').bootstrapSwitch('state');
        if ($('#safe_site').bootstrapSwitch('state'))
            $('#unsafe-site').hide();

        $('#safe_site').on('switchChange.bootstrapSwitch', function (event, state) {
            $('#unsafe-site').toggle();
        });

        $('#open_docs').click(function () {
            $('#docs').show();
        });

        $('#close_docs').click(function () {
            $('#docs').hide();
        });
    });
</script>
@stop

