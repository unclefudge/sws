@inject('maintenanceCategories', 'App\Http\Utilities\MaintenanceCategories')
@inject('maintenanceWarranty', 'App\Http\Utilities\MaintenanceWarranty')
@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        <li><a href="/site/maintenance">Maintenance Register</a><i class="fa fa-circle"></i></li>
        <li><span>Create</span></li>
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
                            <span class="caption-subject font-green-haze bold uppercase">Maintenance Request</span>
                            <span class="caption-helper"></span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('SiteQa', ['action' => 'Site\SiteMaintenanceController@store', 'class' => 'horizontal-form', 'files' => true]) !!}
                        @include('form-error')

                        {{-- Progress Steps --}}
                        <div class="mt-element-step hidden-sm hidden-xs">
                            <div class="row step-thin" id="steps">
                                <div class="col-md-3 mt-step-col first active">
                                    <div class="mt-step-number bg-white font-grey">1</div>
                                    <div class="mt-step-title uppercase font-grey-cascade">Create</div>
                                    <div class="mt-step-content font-grey-cascade">Create Request</div>
                                </div>
                                <div class="col-md-3 mt-step-col">
                                    <div class="mt-step-number bg-white font-grey">2</div>
                                    <div class="mt-step-title uppercase font-grey-cascade">Photos</div>
                                    <div class="mt-step-content font-grey-cascade">Add photos</div>
                                </div>
                                <div class="col-md-3 mt-step-col">
                                    <div class="mt-step-number bg-white font-grey">3</div>
                                    <div class="mt-step-title uppercase font-grey-cascade">Visit Client</div>
                                    <div class="mt-step-content font-grey-cascade">Schedule visit</div>
                                </div>
                                <div class="col-md-3 mt-step-col last">
                                    <div class="mt-step-number bg-white font-grey">4</div>
                                    <div class="mt-step-title uppercase font-grey-cascade">Review</div>
                                    <div class="mt-step-content font-grey-cascade">Approve/Decline</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-body">

                            <h4>Site Details</h4>
                            <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('site_id', $errors) !!}">
                                        {!! Form::label('site_id', 'Completed Sites', ['class' => 'control-label']) !!}
                                        <select id="site_id" name="site_id" class="form-control select2" style="width:100%">
                                            {!! Auth::user()->authSitesSelect2Options('view.site', old('site_id'), 0) !!}
                                        </select>
                                        {!! fieldErrorMessage('site_id', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('suburb', 'Suburb', ['class' => 'control-label']) !!}
                                        {!! Form::text('suburb', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {!! Form::label('code', 'Site No.', ['class' => 'control-label']) !!}
                                        {!! Form::text('code', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('supervisor', $errors) !!}">
                                        {!! Form::label('supervisor', 'Supervisor', ['class' => 'control-label']) !!}
                                        {!! Form::text('supervisor', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('supervisor', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('completed', 'Prac Completed', ['class' => 'control-label']) !!}
                                        {!! Form::text('completed', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>
                                </div>
                            </div>


                            <h4>Client Contact Details</h4>
                            <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group {!! fieldHasError('contact_name', $errors) !!}">
                                        {!! Form::label('contact_name', 'Name', ['class' => 'control-label']) !!}
                                        {!! Form::text('contact_name', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('contact_name', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group {!! fieldHasError('contact_phone', $errors) !!}">
                                        {!! Form::label('contact_phone', 'Phone', ['class' => 'control-label']) !!}
                                        {!! Form::text('contact_phone', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('contact_phone', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group {!! fieldHasError('contact_email', $errors) !!}">
                                        {!! Form::label('contact_email', 'Email', ['class' => 'control-label']) !!}
                                        {!! Form::text('contact_email', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('contact_email', $errors) !!}
                                    </div>
                                </div>
                            </div>


                            <h4>Request Details</h4>
                            <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                            <div class="row">
                                {{-- Category --}}
                                <div class="col-md-3 ">
                                    <div class="form-group">
                                        {!! Form::label('category_id', 'Category', ['class' => 'control-label']) !!}
                                        {!! Form::select('category_id', $maintenanceCategories::all(), null, ['class' => 'form-control bs-select', 'id' => 'category_id']) !!}
                                    </div>
                                </div>

                                {{-- Warranty --}}
                                <div class="col-md-2 ">
                                    <div class="form-group">
                                        {!! Form::label('warranty', 'Warranty', ['class' => 'control-label']) !!}
                                        {!! Form::select('warranty', $maintenanceWarranty::all(), null, ['class' => 'form-control bs-select', 'id' => 'warranty']) !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Multi File upload -->
                            {{--}}
                            <div id="multifile-div">
                                <div class="note note-warning">
                                    Multiple photos/images can be uploaded with this maintenance request.
                                    <ul>
                                        <li>Once you have selected your files upload them by clicking
                                            <button class="btn dark btn-outline btn-xs" href="javascript:;"><i class="fa fa-upload"></i> Upload</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Select Files</label>
                                            <input id="multifile" name="multifile[]" type="file" multiple class="file-loading">
                                        </div>
                                    </div>
                                </div>
                            </div>--}}


                                    <!-- Items -->
                            <div id="items-div">
                                <br>
                                <div class="row" style="border: 1px solid #e7ecf1; padding: 10px 0px; margin: 0px; background: #f0f6fa; font-weight: bold">
                                    <div class="col-md-12">MAINTENANCE ITEMS</div>
                                </div>
                                <br>
                                @for ($i = 1; $i <= 10; $i++)
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group">{!! Form::textarea("item$i", '', ['rows' => '2', 'class' => 'form-control', 'placeholder' => "Item $i."]) !!}</div>
                                        </div>
                                    </div>
                                @endfor

                                {{-- Extra Fields --}}
                                <button class="btn blue" id="more">More Items</button>
                                <div class="row" id="more_items" style="display: none">
                                    @for ($i = 10 + 1; $i <= 25; $i++)
                                        <div class="col-md-12">
                                            <div class="form-group">{!! Form::textarea("item$i", null, ['rows' => '2', 'class' => 'form-control', 'placeholder' => "Item $i."]) !!}</div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right">
                            <a href="/site/maintenance" class="btn default"> Back</a>
                            <button type="submit" class="btn green"> Save</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @stop <!-- END Content -->


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
    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name=token]').attr('value')}
    });

    $(document).ready(function () {
        /* Select2 */
        $("#site_id").select2({placeholder: "Select Site", width: "100%"});
        $("#category_id").select2({placeholder: "Select category", width: "100%"});
        //$("#super_id").select2({placeholder: "Select Supervisor", width: "100%"});

        updateFields();

        // On Change Site ID
        $("#site_id").change(function () {
            updateFields();
        });

        $("#more").click(function (e) {
            e.preventDefault();
            $('#more').hide();
            $('#more_items').show();
        });

        /* Bootstrap Fileinput */
        /*
         $("#multifile").fileinput({
         //uploadUrl: "/site/maintenance/upload/", // server upload action
         uploadAsync: true,
         //allowedFileExtensions: ["image"],
         allowedFileTypes: ["image"],
         browseClass: "btn blue",
         browseLabel: "Browse",
         browseIcon: "<i class=\"fa fa-folder-open\"></i> ",
         //removeClass: "btn red",
         removeLabel: "",
         removeIcon: "<i class=\"fa fa-trash\"></i> ",
         layoutTemplates: {
         main1: '<div class="input-group {class}">\n' +
         '   {caption}\n' +
         '   <div class="input-group-btn">\n' +
         '       {remove}\n' +
         '       {browse}\n' +
         '   </div>\n' +
         '</div>\n' +
         '<div class="kv-upload-progress hide" style="margin-top:10px"></div>\n' +
         '{preview}\n'
         },
         }); */

        /* Bootstrap Fileinput */
        $("#multifile").fileinput({
            //uploadUrl: "/site/maintenance/upload/", // server upload action
            uploadAsync: true,
            //allowedFileExtensions: ["image"],
            allowedFileTypes: ["image"],
            browseClass: "btn blue",
            browseLabel: "Browse",
            browseIcon: "<i class=\"fa fa-folder-open\"></i> ",
            //removeClass: "btn red",
            removeLabel: "",
            removeIcon: "<i class=\"fa fa-trash\"></i> ",
            uploadClass: "btn dark",
            uploadIcon: "<i class=\"fa fa-upload\"></i> ",
            uploadExtraData: {
                "site_id": site_id,
            },
            layoutTemplates: {
                main1: '<div class="input-group {class}">\n' +
                '   {caption}\n' +
                '   <div class="input-group-btn">\n' +
                '       {remove}\n' +
                '       {upload}\n' +
                '       {browse}\n' +
                '   </div>\n' +
                '</div>\n' +
                '<div class="kv-upload-progress hide" style="margin-top:10px"></div>\n' +
                '{preview}\n'
            },
        });

        $('#multifile').on('filepreupload', function (event, data, previewId, index, jqXHR) {
            data.form.append("site_id", $("#site_id").val());
        });

        /*
         $('#multifile').on('filepreupload', function (event, data, previewId, index, jqXHR) {
         data.form.append("site_id", $("#site_id").val());
         });*/

        function updateFields() {
            var site_id = $("#site_id").select2("val");
            $("#completed").val('');
            $('#multifile-div').hide();
            $('#items-div').hide();

            if (site_id != '') {
                $('#multifile-div').show();
                $('#items-div').show();
                $.ajax({
                    url: '/site/data/details/' + site_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $("#suburb").val(data.suburb);
                        $("#code").val(data.code);
                        console.log(data.suburb);
                    },
                })

                $.ajax({
                    url: '/site/maintenance/data/prac_completion/' + site_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        var year = data.date.substring(0, 4);
                        var month = data.date.substring(5, 7);
                        var day = data.date.substring(8, 10);
                        $("#completed").val(day + '/' + month + '/' + year);
                    },
                })

                $.ajax({
                    url: '/site/maintenance/data/site_super/' + site_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $("#supervisor").val(data);
                        //$('#supervisor').trigger('change.select2');
                    },
                })
                //alert('h');
            }
        }
    });
</script>
@stop

