<?php
$class = ['class' => 'form-control bs-select'];
$sub1 = (Auth::user()->company->subscription > 0) ? 1 : 0;
$sub2 = (Auth::user()->company->subscription > 1) ? 1 : 0;
$plan = (Auth::user()->company->addon('planner')) ? 1 : 0;
$cc = (Auth::user()->isCC()) ? 1 : 0;

$dis = (Auth::user()->allowed2('edit.settings', $role)) ? false : true;
$cid = Auth::user()->company_id;
$rec = $role;
/*$sub1 = 1;
$sub2 = 1;
$plan = 0;
$cc = 1;*/
?>
@inject('companyDocTypes', 'App\Http\Utilities\CompanyDocTypes')
@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-cog"></i> Role Management</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><a href="/settings">Settings</a><i class="fa fa-circle"></i></li>
        <li><a href="/settings/role">Role Management</a><i class="fa fa-circle"></i></li>
        <li><span>Edit role</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        {!! Form::model($role, ['method' => 'PATCH', 'action' => ['Misc\RoleController@update', $role->id], 'class' => 'horizontal-form']) !!}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-pencil "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Edit Role</span>
                            <span class="caption-helper"> - {{ $role->name }}</span>
                        </div>
                    </div>

                    <div class="portlet-body form">
                        @include('form-error')
                        <div class="form-body">
                            <h1 style="margin-top: 0">{{ $role->name }}</h1>
                            <h3 class="font-green form-section">Role</h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                        {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                                        {!! Form::text('name', null, ['class' => 'form-control', ($dis) ? 'disabled' : '']) !!}
                                        {!! fieldErrorMessage('name', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group {!! fieldHasError('description', $errors) !!}">
                                        {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
                                        {!! Form::text('description', null, ['class' => 'form-control', ($dis) ? 'disabled' : '']) !!}
                                        {!! fieldErrorMessage('description', $errors) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right">
                            <a href="/settings/role" class="btn default"> Back</a>
                            @if(Auth::user()->allowed2('edit.settings', $role))
                                <button type="submit" class="btn green">Save</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Permission Description --}}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-green-haze bold uppercase">Permissions</span>
                        </div>
                    </div>

                    <div class="portlet-body form">
                        <div class="note note-warning">
                            <p>Permissions are separated into 5 categories (View, Edit, Create, Delete, Sign Off) and these determine what actions users can do for associated records or
                                function.</p>
                            <p><br>For the View / Edit categories, users are able to be restricted to only certain records, which are as follows:</p>
                            <ul>

                                @if ($sub2)
                                    <li><span style="float: left; width:150px">All</span>All records including any 'child' companies.</li>
                                    <li><span style="float: left; width:150px">Our Company</span>Only records that relate specifically to your own company (excludes 'child' company records).
                                    </li>
                                @else
                                    <li><span style="float: left; width:150px">All</span>All records.</li>
                                @endif
                                <li><span style="float: left; width:150px">Supervisor for</span>Only records that relate to a 'site' which the user is a supervisor for.</li>
                                @if ($plan)
                                    <li><span style="float: left; width:150px">Planned for</span>Only records that relate to a 'site' which the user is planned for.</li>
                                @endif
                                @if ($sub2)
                                    <li><span style="float: left; width:150px">Own Company</span>Only records that relate to the 'child' company the user belongs to.</li>
                                @endif
                                <li><span style="float: left; width:150px">Individual Only</span>Only records that the user created or relate specifically to them.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Management --}}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-green-haze bold uppercase">Management</span>
                        </div>
                    </div>

                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped">
                                        <tr>
                                            <td style="background: #FFF; border: 0px #e7ecf1; font-size: 18px; font-weight: 300; padding: 0;"></td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">View</td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Edit</td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Create</td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Delete
                                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                                   data-content="For record integrity most data can't be deleted but users may be given access to archive / resolve it instead. For data that is actually deleted you will be asked to 'confirm'"
                                                   data-original-title="Delete"> <i class="fa fa-question-circle font-grey-silver"></i> </a></td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Sign Off
                                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                                   data-content="Certain actions or updates to the record are required to be 'Signed Off' by an authorised user."
                                                   data-original-title="Sign Off"> <i class="fa fa-question-circle font-grey-silver"></i> </a></td>
                                        </tr>
                                    </table>
                                    <h5 class="font-green-haze" style="font-size: 16px">Companies
                                        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                           data-content="Grants ability to view or modify your company information or any 'child' company."
                                           data-original-title="Company"> <i class="fa fa-question-circle font-grey-silver"></i> </a></h5>
                                    <table class="table table-bordered table-striped">
                                        @if ($sub2 && !$role->external)
                                            <tr>
                                                <td>Company Record</td>
                                                <td width="30%" colspan="2"></td>
                                                <td width="15%">{!! permSelect('add.company', 'add', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('del.company', 'arc', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">&nbsp;</td>

                                            </tr>
                                        @endif
                                        @if (!$role->external)
                                            <tr>
                                                <td>Company Details @if ($sub2) <br><span class="font-red">Disabled for 'child' company roles</span>@endif</td>
                                                <td width="15%">{!! permSelect('view.company', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('edit.company', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                @if ($sub2)
                                                    <td width="30%" colspan="2"></td>
                                                    <td width="15%">{!! permSelect('sig.company', 'sig', $rec, $cid, $dis) !!}</td>
                                                @else
                                                    <td width="45%" colspan="3"></td>
                                                @endif


                                            </tr>
                                            <tr>
                                                <td>Business Details @if ($sub2)<br><span class="font-red">Disabled for 'child' company roles</span>@endif</td>
                                                <td width="15%">{!! permSelect('view.company.acc', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('edit.company.acc', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                @if ($sub2)
                                                    <td width="30%" colspan="2"></td>
                                                    <td width="15%">{!! permSelect('sig.company.acc', 'sig', $rec, $cid, $dis) !!}</td>
                                                @else
                                                    <td width="45%" colspan="3"></td>
                                                @endif
                                            </tr>
                                        @endif
                                        @if($plan)
                                            <tr>
                                                <td>Construction</td>
                                                <td width="15%">{!! permSelect('view.company.con', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('edit.company.con', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                @if (!$role->external)
                                                    <td width="30%" colspan="2"></td>
                                                    <td width="15%">{!! permSelect('sig.company.con', 'sig', $rec, $cid, $dis) !!}</td>
                                                @else
                                                    <td width="45%" colspan="3"></td>
                                                @endif
                                            </tr>
                                        @endif
                                        @if ($sub2)
                                            <tr>
                                                <td>WHS Compliance</td>
                                                <td width="15%">{!! permSelect('view.company.whs', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('edit.company.whs', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                @if (!$role->external)
                                                    <td width="30%" colspan="2"></td>
                                                    <td width="15%">{!! permSelect('sig.company.whs', 'sig', $rec, $cid, $dis) !!}</td>
                                                @else
                                                    <td width="45%" colspan="3"></td>
                                                @endif
                                            </tr>
                                            <tr>
                                                <td>Company Leave</td>
                                                <td width="15%">{!! permSelect('view.company.leave', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('edit.company.leave', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="45%" colspan="3"></td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right">
                            <a href="/settings/role" class="btn default"> Back</a>
                            @if(Auth::user()->allowed2('edit.settings', $rec))
                                <button type="submit" class="btn green">Save</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-green-haze bold uppercase">Documents</span>
                        </div>
                    </div>

                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped">
                                        <tr>
                                            <td style="background: #FFF; border: 0px #e7ecf1; font-size: 18px; font-weight: 300; padding: 0;"></td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">View</td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Edit</td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Upload</td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Delete
                                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                                   data-content="For record integrity most data can't be deleted but users may be given access to archive / resolve it instead. For data that is actually deleted you will be asked to 'confirm'"
                                                   data-original-title="Delete"> <i class="fa fa-question-circle font-grey-silver"></i> </a></td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Sign Off
                                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                                   data-content="Certain actions or updates to the record are required to be 'Signed Off' by an authorised user."
                                                   data-original-title="Sign Off"> <i class="fa fa-question-circle font-grey-silver"></i> </a></td>
                                        </tr>
                                    </table>
                                    <h5 class="font-green-haze" style="font-size: 16px">Public Documents</h5>
                                    <table class="table table-bordered table-striped">
                                        @foreach ($companyDocTypes::all() as $doc_type => $doc_name)
                                            <tr>
                                                <td>{{ $doc_name }}
                                                    <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                                       data-content="{!! $companyDocTypes::docNames($doc_type, 0) !!}" data-original-title="Documents"> <i
                                                                class="fa fa-question-circle font-grey-silver"></i> </a></td>
                                                <td width="15%">{!! permSelect("view.docs.$doc_type.pub", ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect("edit.docs.$doc_type.pub", ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect("add.docs.$doc_type.pub", 'up', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect("del.docs.$doc_type.pub", 'arc', $rec, $cid, $dis) !!}</td>
                                                @if (!$role->external)
                                                    <td width="15%">{!! permSelect("sig.docs.$doc_type.pub", 'sig', $rec, $cid, $dis) !!}</td>
                                                @else
                                                    <td width="15%"></td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </table>
                                    @if ($cc)
                                        <h5 class="font-green-haze" style="font-size: 16px">Private Documents</h5>
                                        <table class="table table-bordered table-striped">
                                            @foreach ($companyDocTypes::all() as $doc_type => $doc_name)
                                                <tr>
                                                    <td>{{ $doc_name }}
                                                        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                                           data-content="{!! $companyDocTypes::docNames('acc', 1) !!}" data-original-title="Documents"> <i
                                                                    class="fa fa-question-circle font-grey-silver"></i> </a></td>
                                                    <td width="15%">{!! permSelect("view.docs.$doc_type.pri", ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect("edit.docs.$doc_type.pri", ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect("add.docs.$doc_type.pri", 'up', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect("del.docs.$doc_type.pri", 'arc', $rec, $cid, $dis) !!}</td>
                                                    @if (!$role->external)
                                                        <td width="15%">{!! permSelect("sig.docs.$doc_type.pri", 'sig', $rec, $cid, $dis) !!}</td>
                                                    @else
                                                        <td width="15%"></td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </table>
                                    @endif
                                </div>
                            </div>
                            <div class="form-actions right">
                                <a href="/settings/role" class="btn default"> Back</a>
                                @if(Auth::user()->allowed2('edit.settings', $rec))
                                    <button type="submit" class="btn green">Save</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-green-haze bold uppercase">Edit Role</span>
                        </div>
                    </div>

                    <div class="portlet-body form">
                        <div class="form-body">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        -->

        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-green-haze bold uppercase"> Legacy Permissions</span>
                        </div>
                    </div>

                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped">
                                        <tr>
                                            <td style="background: #FFF; border: 0px #e7ecf1; font-size: 18px; font-weight: 300; padding: 0;">Permissions</td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">View</td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Edit</td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Create</td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Delete
                                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                                   data-content="For record integrity most data can't be deleted but users may be given access to archive / resolve it instead. For data that is actually deleted you will be asked to 'confirm'"
                                                   data-original-title="Delete"> <i class="fa fa-question-circle font-grey-silver"></i> </a></td>
                                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Sign Off
                                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                                   data-content="Certain actions or updates to the record are required to be 'Signed Off' by an authorised user."
                                                   data-original-title="Sign Off"> <i class="fa fa-question-circle font-grey-silver"></i> </a></td>
                                        </tr>
                                    </table>
                                    <h5 class="font-green-haze" style="font-size: 16px">User
                                        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                           data-content="Grants ability to view or modify users that belong to your company or any 'child' company."
                                           data-original-title="User"> <i class="fa fa-question-circle font-grey-silver"></i> </a></h5>
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <td>User</td>
                                            <td width="15%">{!! permSelect('view.user', ($sub2) ? 'our' : 'all', $rec, $cid, $dis) !!}</td>
                                            <td width="15%">{!! permSelect('edit.user', ($sub2) ? 'our' : 'all', $rec, $cid, $dis) !!}</td>
                                            <td width="15%">{!! permSelect('add.user', 'add', $rec, $cid, $dis) !!}</td>
                                            <td width="15%">{!! permSelect('del.user', 'arc', $rec, $cid, $dis) !!}</td>
                                            <td width="15%">{!! permSelect('sig.user', 'sig', $rec, $cid, $dis) !!}</td>
                                        </tr>
                                    </table>


                                    @if ($sub1)
                                        <h5 class="font-green-haze" style="font-size: 16px">Work Site
                                            <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                               data-content="Grants ability to view or modify work sites and relevant trades/tasks/superviors required on the sites."
                                               data-original-title="Work Sites"> <i class="fa fa-question-circle font-grey-silver"></i> </a></h5>
                                        <table class="table table-bordered table-striped">
                                            <tr>
                                                <td>Site @if ($sub2 && !$role->external) <br><span class="font-red">Edit/Create/Delete functions<br>Disabled for 'child' company roles</span>@endif</td>
                                                <td width="15%">{!! permSelect('view.site', ($plan) ? 'super.plan' : 'super', $rec, $cid, $dis) !!}</td>
                                                @if (!$role->external)
                                                    <td width="15%">{!! permSelect('edit.site', ($plan) ? 'super.plan' : 'super', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('add.site', 'add', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('del.site', 'arc', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%"></td>
                                                @else
                                                    <td width="60%" colspan="4"></td>
                                                @endif
                                            </tr>
                                            @if (!$role->external)
                                                @if ($plan)
                                                    <tr>
                                                        <td>Trades / Tasks @if ($sub2) <br><span class="font-red">Disabled for 'child' company roles</span>@endif</td>
                                                        <td width="15%">{!! permSelect('view.trade', 'all', $rec, $cid, $dis) !!}</td>
                                                        <td width="15%">{!! permSelect('edit.trade', 'all', $rec, $cid, $dis) !!}</td>
                                                        <td width="15%">{!! permSelect('add.trade', 'add', $rec, $cid, $dis) !!}</td>
                                                        <td width="15%">{!! permSelect('del.trade', 'arc', $rec, $cid, $dis) !!}</td>
                                                        <td width="15%"></td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td>Site Supervisors @if ($sub2) <br><span class="font-red">Disabled for 'child' company roles</span>@endif</td>
                                                    <td width="15%">{!! permSelect('view.area.super', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.area.super', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="45%" colspan="3"></td>
                                                </tr>
                                            @endif
                                            @if ($cc && !$role->external)
                                                <tr>
                                                    <td>Admin Info<br><span class="font-grey-silver">Cape Cod Only</span></td>
                                                    <td width="15%">{!! permSelect('view.site.admin', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.site.admin', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="45%" colspan="3"></td>
                                                </tr>
                                            @endif
                                        </table>

                                        @if ($plan)
                                            <h5 class="font-green-haze" style="font-size: 16px">Planners
                                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                                   data-content="Grants ability to view or modify planners for work sites that belong to your company."
                                                   data-original-title="Planners"> <i class="fa fa-question-circle font-grey-silver"></i>
                                                </a>
                                            </h5>
                                            <table class="table table-bordered table-striped">
                                                <tr>
                                                    <td>Weekly</td>
                                                    <td width="15%">{!! permSelect('view.weekly.planner', 'super.plan', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.weekly.planner', 'super.plan', $rec, $cid, $dis) !!}</td>
                                                    <td width="45%" colspan="3"></td>
                                                </tr>
                                                <tr>
                                                    <td>Site</td>
                                                    <td width="15%">{!! permSelect('view.site.planner', 'super.plan', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.site.planner', 'super.plan', $rec, $cid, $dis) !!}</td>
                                                    <td width="45%" colspan="3"></td>
                                                </tr>
                                                <tr>
                                                    <td>Trade</td>
                                                    <td width="15%">{!! permSelect('view.trade.planner', 'super.plan', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.trade.planner', 'super.plan', $rec, $cid, $dis) !!}</td>
                                                    <td width="45%" colspan="3"></td>
                                                </tr>
                                            </table>

                                            <h5 class="font-green-haze" style="font-size: 16px">Attendance / Compliance
                                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                                   data-content="Grants ability to view or edit attendance for users which belong to your company work sites."
                                                   data-original-title="Attendance / Compliance"> <i
                                                            class="fa fa-question-circle font-grey-silver"></i>
                                                </a>
                                            </h5>
                                            <table class="table table-bordered table-striped">
                                                <tr>
                                                    <td>Attendance</td>
                                                    <td width="15%">{!! permSelect('view.attendance', ($sub2) ? 'super.company' : 'super.individual', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.attendance', ($sub2) ? 'super.company' : 'super.individual', $rec, $cid, $dis) !!}</td>
                                                    <td width="45%" colspan="3"></td>
                                                </tr>
                                                @if($cc)
                                                    <tr>
                                                        <td>Compliance<br><span class="font-grey-silver">Cape Cod Only</span></td>
                                                        <td width="15%">{!! permSelect('view.compliance', ($sub2) ? 'super.company' : 'super', $rec, $cid, $dis) !!}</td>
                                                        <td width="15%">{!! permSelect('edit.compliance', ($sub2) ? 'super.company' : 'super', $rec, $cid, $dis) !!}</td>
                                                        <td width="45%" colspan="3"></td>
                                                    </tr>
                                                @endif
                                            </table>
                                        @endif

                                        <h5 class="font-green-haze" style="font-size: 16px">Site Documents
                                            <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                               data-content="Grants ability to view / modify documents which belong to your company."
                                               data-original-title="Site Documents"> <i class="fa fa-question-circle font-grey-silver"></i>
                                            </a>
                                        </h5>

                                        <table class="table table-bordered table-striped">
                                            <tr>
                                                <td>Accident Reports</td>
                                                @if ($plan)
                                                    <td width="15%">{!! permSelect('view.site.accident', ($sub2) ? 'every' : 'super.individual', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.site.accident', ($sub2) ? 'every' : 'super.individual', $rec, $cid, $dis) !!}</td>
                                                @else
                                                    <td width="15%">{!! permSelect('view.site.accident', ($sub2) ? 'every-plan' : 'super.individual', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.site.accident', ($sub2) ? 'every-plan' : 'super.individual', $rec, $cid, $dis) !!}</td>
                                                @endif
                                                <td width="15%">{!! permSelect('add.site.accident', 'add', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('del.site.accident', 'res', $rec, $cid, $dis) !!}</td>
                                                <td width="15%"></td>
                                            </tr>
                                            <tr>
                                                <td>Hazard Reports</td>
                                                @if ($plan)
                                                    <td width="15%">{!! permSelect('view.site.hazard', ($sub2) ? 'every' : 'super.individual', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.site.hazard', ($sub2) ? 'every' : 'super.individual', $rec, $cid, $dis) !!}</td>
                                                @else
                                                    <td width="15%">{!! permSelect('view.site.hazard', ($sub2) ? 'every-plan' : 'super.individual', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.site.hazard', ($sub2) ? 'every-plan' : 'super.individual', $rec, $cid, $dis) !!}</td>
                                                @endif
                                                <td width="15%">{!! permSelect('add.site.hazard', 'add', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('del.site.hazard', 'res', $rec, $cid, $dis) !!}</td>
                                                <td width="15%"></td>
                                            </tr>
                                            <tr>
                                                <td>Safety (Risk / Hazardous Materials)</td>
                                                <td width="15%">{!! permSelect('view.safety.doc', ($plan) ? 'super.plan' : 'super', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('edit.safety.doc', ($plan) ? 'super.plan' : 'super', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('add.safety.doc', 'add', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('del.safety.doc', 'del', $rec, $cid, $dis) !!}</td>
                                                <td width="15%"></td>
                                            </tr>
                                            <tr>
                                                <td>General / Plans</td>
                                                <td width="15%">{!! permSelect('view.site.doc', ($plan) ? 'super.plan' : 'super', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('edit.site.doc', ($plan) ? 'super.plan' : 'super', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('add.site.doc', 'add', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('del.site.doc', 'del', $rec, $cid, $dis) !!}</td>
                                                <td width="15%"></td>
                                            </tr>
                                            @if($cc)
                                                <tr>
                                                    <td>Quality Assurance Reports<br><span class="font-grey-silver">Cape Cod Only</span></td>
                                                    <td width="15%">{!! permSelect('view.site.qa', 'super', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.site.qa', 'super', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('add.site.qa', 'add', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('del.site.qa', 'res', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('sig.site.qa', 'sig', $rec, $cid, $dis) !!}</td>
                                                </tr>
                                                <tr>
                                                    <td>Asbestos Notifications<br><span class="font-grey-silver">Cape Cod Only</span></td>
                                                    <td width="15%">{!! permSelect('view.site.asbestos', 'super', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.site.asbestos', 'super', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('add.site.asbestos', 'add', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('del.site.asbestos', 'res', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%"></td>
                                                </tr>
                                            @endif
                                        </table>


                                        <h5 class="font-green-haze" style="font-size: 16px">Other Documents
                                            <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                               data-content="Grants ability to view or modify documents which belong to your company."
                                               data-original-title="General Documents"> <i class="fa fa-question-circle font-grey-silver"></i>
                                            </a>
                                        </h5>
                                        <table class="table table-bordered table-striped">
                                            <tr>
                                                <td>Safe Work Method Statements</td>
                                                <td width="15%">{!! permSelect('view.wms', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('edit.wms', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('add.wms', 'add', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('del.wms', 'arc', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('sig.wms', 'sig', $rec, $cid, $dis) !!}</td>
                                            </tr>
                                            <tr>
                                                <td>Toolbox Talks</td>
                                                <td width="15%">{!! permSelect('view.toolbox', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('edit.toolbox', ($sub2) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('add.toolbox', 'add', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('del.toolbox', 'res', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('sig.toolbox', 'sig', $rec, $cid, $dis) !!}</td>
                                            </tr>
                                            <tr>
                                                <td>Safety Data Sheets (SDS)</td>
                                                <td width="15%">{!! permSelect('view.sds', 'all', $rec, $cid, $dis) !!}</td>
                                                @if (false)
                                                    <td width="15%">{!! permSelect('edit.sds', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('add.sds', 'add', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('del.sds', 'del', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%"></td>
                                                @else
                                                    <td width="60%" colspan="4"></td>
                                                @endif
                                            </tr>
                                        </table>

                                        <h5 class="font-green-haze" style="font-size: 16px">Management Reports / Exports
                                            <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                               data-content="Grants ability to view management reports and exports which belong to your company."
                                               data-original-title="Management Reports / Exports"> <i class="fa fa-question-circle font-grey-silver"></i>
                                            </a>
                                        </h5>

                                        @if ($sub1)
                                            <table class="table table-bordered table-striped">
                                                <tr>
                                                    <td>Management Reports</td>
                                                    <td width="15%">{!! permSelect('view.manage.report', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="60%" colspan="4"></td>
                                                </tr>
                                                @if ($cc)
                                                    <tr>
                                                        <td>Export Site Data</td>
                                                        <td width="15%">{!! permSelect('view.site.export', 'super', $rec, $cid, $dis) !!}</td>
                                                        <td width="60%" colspan="4"></td>
                                                    </tr>
                                                @endif
                                            </table>
                                        @endif

                                        @if ($cc)
                                            <h5 class="font-green-haze" style="font-size: 16px">Messages / Alerts
                                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                                   data-content="Grants ability to communicate via messages / alert to other users which belong to your company or child company."
                                                   data-original-title="Messages / Alerts"> <i class="fa fa-question-circle font-grey-silver"></i>
                                                </a>
                                            </h5>

                                            <table class="table table-bordered table-striped">
                                                <tr>
                                                    <td>Alert Nofications<br><span class="font-grey-silver">Cape Cod Only</span></td>
                                                    <td width="15%">{!! permSelect('view.notify', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.notify', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('add.notify', 'add', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('del.notify', 'del', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%"></td>
                                                </tr>
                                                <tr>
                                                    <td>Safety Tips<br><span class="font-grey-silver">Cape Cod Only</span></td>
                                                    <td width="15%">{!! permSelect('view.safetytip', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.safetytip', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('add.safetytip', 'add', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('del.safetytip', 'del', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%"></td>
                                                </tr>

                                                {{--
                                                <tr>
                                                    <td>To Do Tasks</td>
                                                    <td width="15%">{!! permSelect('view.todo', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.todo', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('add.todo', 'add', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%"></td>
                                                    <td width="15%"></td>
                                                </tr>
                                                --}}
                                            </table>
                                        @endif

                                        <h5 class="font-green-haze" style="font-size: 16px">Configuration / Settings
                                            <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                               data-content="Grants ability to view / modify configuration settings for this website"
                                               data-original-title="Configuration / Settings"> <i class="fa fa-question-circle font-grey-silver"></i>
                                            </a>
                                        </h5>

                                        <table class="table table-bordered table-striped">
                                            <tr>
                                                <td>Settings</td>
                                                <td width="15%">{!! permSelect('view.settings', 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('edit.settings', 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="45%" colspan="3"></td>
                                            </tr>
                                            @if ($cc)
                                                <tr>
                                                    <td>Support Ticket Upgrades</td>
                                                    <td width="15%">{!! permSelect('view.support.ticket.upgrade', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('edit.support.ticket.upgrade', 'all', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('add.support.ticket.upgrade', 'add', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%">{!! permSelect('del.support.ticket.upgrade', 'del', $rec, $cid, $dis) !!}</td>
                                                    <td width="15%"></td>
                                                </tr>
                                            @endif
                                            {{--
                                            <tr>
                                                <td>Roles / Permissions</td>
                                                <td width="15%">{!! permSelect('view.role', 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('edit.role', 'all', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('add.role', 'add', $rec, $cid, $dis) !!}</td>
                                                <td width="15%">{!! permSelect('dell.role', 'del', $rec, $cid, $dis) !!}</td>
                                                <td width="15%"></td>
                                            </tr>
                                            --}}
                                        </table>
                                    @endif
                                </div>
                            </div>
                            <div class="form-actions right">
                                <a href="/settings/role" class="btn default"> Back</a>
                                @if(Auth::user()->allowed2('edit.settings', $role))
                                    <button type="submit" class="btn green">Save</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    @stop <!-- END Content -->


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
@stop

