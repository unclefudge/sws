@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-life-ring"></i> Safety Data Sheets</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><span></span></li>
        <li><a href="/safety/doc/sds">Safety Data Sheets</a><i class="fa fa-circle"></i></li>
        <li><span>Edit</span></li>
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
                            <span class="caption-subject font-green-haze bold uppercase">Edit SDS </span>
                        </div>
                        <div class="actions">
                            <a href="" class="btn btn-circle btn-icon-only btn-default collapse"> </a>
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('safetydoc', ['method' => 'PATCH', 'action' => ['Safety\SdsController@update', $doc->id], 'class' => 'horizontal-form', 'files' => true]) !!}
                        @include('form-error')

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group {!! fieldHasError('category_id', $errors) !!}">
                                        {!! Form::label('category_id', 'Category', ['class' => 'control-label']) !!}
                                        {!! Form::select('category_id', App\Models\Safety\SafetyDocCategory::sdsCats(),
                                             $doc->category_id, ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('category_id', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-1 pull-right hidden-sm hidden-xs">
                                    <a href="/filebank/whs/sds/{{ $doc->attachment }}" target="_blank"><i class="fa fa-bold fa-4x fa-file-text-o" style="margin-top: 25px"></i></a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                        {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                                        {!! Form::text('name', $doc->name, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4 pull-right">
                                    <button type="button" class="btn blue pull-right" style="margin-top: 25px" id="change_file"> Change File</button>
                                </div>
                                <div class="col-xs-2 pull-right visible-sm visible-xs">
                                    <a href="/filebank/whs/sds/{{ $doc->attachment }}" target="_blank"><i class="fa fa-bold fa-4x fa-file-text-o" style="margin-top: 25px"></i></a>
                                </div>
                            </div>
                            <!-- File upload -->
                            <div class="row" style="display: none" id="uploadfile-div">
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('uploadfile', $errors) !!}">
                                        <label class="control-label">Select File</label>
                                        <input id="uploadfile" name="uploadfile" type="file" class="file-loading">
                                        {!! fieldErrorMessage('uploadfile', $errors) !!}
                                    </div>
                                </div>
                            </div>
                            <h3 class="form-section"></h3>
                            <!-- Notes -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group {!! fieldHasError('notes', $errors) !!}">
                                        {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                                        {!! Form::textarea('notes', $doc->notes, ['rows' => '2', 'class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('notes', $errors) !!}
                                        <span class="help-block"> For internal use only </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions right">
                                <button type="submit" name="back" value="back" class="btn default"> Back</button>
                                <button type="submit" name="save" value="save" class="btn green">Save</button>
                            </div>
                        </div> <!--/form-body-->
                        {!! Form::close() !!}
                                <!-- END FORM-->
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
                {!! $doc->displayUpdatedBy() !!}
            </div>
        </div>
        <!-- END PAGE CONTENT INNER -->
    </div>
    @stop

    @section('page-level-plugins-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/css/libs/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
    @stop

    @section('page-level-plugins')
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/js/libs/fileinput.min.js"></script>
    @stop

    @section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
    <script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            /* Bootstrap Fileinput */
            $("#uploadfile").fileinput({
                showUpload: false,
                allowedFileExtensions: ["pdf"],
                browseClass: "btn blue",
                browseLabel: "Browse",
                browseIcon: "<i class=\"fa fa-folder-open\"></i> ",
                //removeClass: "btn btn-danger",
                removeLabel: "",
                removeIcon: "<i class=\"fa fa-trash\"></i> ",
                uploadClass: "btn btn-info",
            });

            $("#change_file").click(function (){
                $('#attachment-div').hide();
                $('#uploadfile-div').show();
            });

        });

    </script>
@stop