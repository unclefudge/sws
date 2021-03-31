@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><span>Site Roster</span></li>
    </ul>
@stop


@section('content')
    <style>
        .aside {
            z-index: 9999;
        }
    </style>

    <app-attend></app-attend>

    <!-- loading Spinner -->
    <div v-show="xx.showSpinner" style="background-color: #FFF; padding: 20px;">
        <div class="loadSpinnerOverlay">
            <div class="loadSpinner"><i class="fa fa-spinner fa-pulse fa-2x fa-fw margin-bottom"></i> Loading...</div>
        </div>
    </div>

    <template id="attend-template">
        <input v-model="xx.params.date" type="hidden" value="{{ $date }}">
        <input v-model="xx.params.supervisor_id" type="hidden" value="{{ $supervisor_id }}">
        <input v-model="xx.params.site_id" type="hidden" value="{{ $site_id }}">
        <input v-model="xx.params.site_start" type="hidden" value="{{ $site_start }}">
        <input v-model="xx.user_company_id" type="hidden" value="{{ Auth::user()->company_id }}">
        <div class="page-content-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light ">
                        <div class="portlet-title tabbable-line">
                            <div class="caption font-dark">
                                <i class="icon-layers"></i>
                                <span class="caption-subject bold uppercase font-green-haze"> Site Roster</span>
                            </div>
                            <div class="actions">
                                <div class="actions">
                                    @if (Auth::user()->hasPermission2('view.trade.planner'))
                                        <button v-on:click="gotoURL('/planner/transient')" class="btn btn-circle btn-icon-only btn-default" style="margin: 3px">L</button>
                                    @endif
                                    <button class="btn btn-circle btn-icon-only btn-default grey-steel disabled" style="margin: 3px">R</button>
                                    @if (Auth::user()->hasPermission2('view.site.planner'))
                                        <button v-on:click="gotoURL('/planner/site')" class="btn btn-circle btn-icon-only btn-default" style="margin: 3px">S</button>
                                    @endif
                                    @if (Auth::user()->hasPermission2('view.trade.planner'))
                                        <button v-on:click="gotoURL('/planner/trade')" class="btn btn-circle btn-icon-only btn-default" style="margin: 3px">T</button>
                                    @endif
                                    @if (Auth::user()->hasPermission2('view.weekly.planner'))
                                        <button v-on:click="gotoURL('/planner/weekly')" class="btn btn-circle btn-icon-only btn-default" style="margin: 3px">W</button>
                                    @endif
                                    <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen" style="margin: 3px"></a>
                                </div>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row" style="padding-bottom: 5px">
                                <div class="col-md-3">
                                    <select-picker :name.sync="xx.params.site_id" :options.sync="xx.sel_site" :function="getDayPlan"></select-picker>
                                </div>
                                <div class="col-md-5 text-center"><h4 class="bold font-green-haze">@{{ xx.current_date | formatDateFull }}</h4></div>
                                <div class="col-md-4 pull-right">
                                    <div class="btn-group btn-group-circle pull-right">
                                        <button v-on:click="changeDay('-')" class="btn blue-hoki">Prev Day</button>
                                        <button v-on:click="changeDay('today')" class="btn blue-dark">Today</button>
                                        <button v-on:click="changeDay('+')" class="btn blue-hoki">Next Day</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table v-show="xx.rostered.length || xx.unrostered.length" class="table table-striped table-bordered table-hover order-column">
                                        <thead>
                                        <tr class="mytable-header">
                                            <th width="5%"></th>
                                            <th width="50%"> Name </th>
                                            <th> Task(s)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Rostered Entities -->
                                        <template v-for="entity in xx.rostered">
                                            <tr v-if="xx.user_company_id == '3' || (xx.user_company_id == entity.entity_id && entity.entity_type == 'c')">
                                                <!-- Open / close Icon -->
                                                <td class="text-center">
                                                    <div v-if="entity.entity_type == 'c'">
                                                        <span v-if="!futureDate(xx.current_date) && entity.open" v-on:click="entity.open = ! entity.open" class="finger">
                                                            <i class="fa fa-user" style="color: #e7505a;"></i></span>
                                                        <span v-if="!futureDate(xx.current_date) && !entity.open" v-on:click="entity.open = ! entity.open" class="finger">
                                                            <i class="fa fa-user" style="color: #32c5d2;"></i></span>
                                                        <span v-if="futureDate(xx.current_date)"><i class="fa fa-user font-grey-silver"></i></span>
                                                    </div>

                                                </td>
                                                <!-- Entity Name + rostered user -->
                                                <td>
                                                    <span v-else style="font-weight:600" class="@{{ entityClass(entity) }}">@{{ entity.entity_name }}</span>
                                                    <br>
                                                    <small>
                                                        <template v-for="(index, user) in entity.attendance">
                                                            <span v-if="user.attended">@{{ user.name }} (@{{ user.attended | formatTime }}),</span>
                                                            <span v-if="!user.attended && user.roster_id" class="font-grey-silver">@{{ user.name }},</span>
                                                        </template>
                                                    </small>
                                                </td>
                                                <td>@{{ entity.tasks }}</td>
                                            </tr>
                                            <!-- Hideable dropdown of Companies User -->
                                            <tr v-if="entity.open" style="background-color: #444D58" class="nohover">
                                                <td colspan="3" style="padding: 3px 7px 0px 7px">
                                                    <h4 v-if="xx.current_date != xx.today || xx.user_company_id != '3'" class="font-white"><i class="fa fa-bars"></i> Attendance - @{{ entity.entity_name }}</h4>
                                                    <h4 v-else class="font-white"><i class="fa fa-bars"></i> Attendance - @{{ entity.entity_name }}
                                                        <span class="visible-xs"><br></span>
                                                        <button v-on:click="checkall(entity, 'del')" class="btn green btn-outline btn-sm pull-right" style="margin-top: -7px">
                                                            <i class="fa fa-minus"></i> Uncheck all
                                                        </button>
                                                        <button v-on:click="checkall(entity, 'add')" class="btn green btn-outline btn-sm pull-right" style="margin-top: -7px">
                                                            <i class="fa fa-plus"></i> Check all
                                                        </button>
                                                    </h4>
                                                    <table class="table table-striped table-bordered table-hover order-column" style="margin-bottom: 10px">
                                                        <!-- Past Dates -->
                                                        <tbody v-if="pastDate(xx.current_date) || xx.user_company_id != '3'">
                                                        <template v-for="user in entity.attendance">
                                                            <tr :class="{ 'font-grey-silver': !user.attended }">
                                                                <td width="30%">@{{ user.name }}</td>
                                                                <td>
                                                                    <span v-if="user.attended">@{{ user.attended | formatTime2 }}</span>
                                                                    <span v-if="user.other_sites" class="font-grey-silver"> @{{ user.other_sites }}</span>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                        </tbody>

                                                        <!-- Today -->
                                                        <tbody v-if="xx.current_date == xx.today && xx.user_company_id == '3'">
                                                        <template v-for="user in entity.attendance">
                                                            <tr>
                                                                <td width="5%" class="text-center" :class="{ 'font-grey-silver': user.attended }">
                                                                    <span v-show="user.roster_id" v-on:click="toggleRoster(user)" :class="{ 'finger': !user.attended}">
                                                                        <i class="fa fa-1.5x fa-check-square-o"></i></span>
                                                                    <span v-show="!user.roster_id" v-on:click="toggleRoster(user)" :class="{ 'finger': !user.attended}">
                                                                        <i class="fa fa-1.5x fa-square-o"></i></span>
                                                                </td>
                                                                <td width="30%">@{{ user.name }}</td>
                                                                <td>
                                                                    <span v-if="user.attended">@{{ user.attended | formatTime2 }}</span>
                                                                    <span v-if="user.other_sites" class="font-grey-silver"> @{{ user.other_sites }}</span>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </template>

                                        <!-- Non Rostered Entities -->
                                        <template v-for="entity in xx.unrostered">
                                            <tr v-if="xx.user_company_id == '3' || (xx.user_company_id == entity.entity_id && entity.entity_type == 'c')">
                                                <td class="text-center">
                                                    <span v-if="entity.open" v-on:click="entity.open = ! entity.open" class="finger"><i class="fa fa-user" style="color: #e7505a;"></i></span>
                                                    <span v-if="!entity.open" v-on:click="entity.open = ! entity.open" class="finger"><i class="fa fa-user" style="color: #32c5d2;"></i></span>
                                                </td>
                                                <td>
                                                    <span style="font-weight:600" class="font-grey-silver">@{{ entity.entity_name }}</span><br>
                                                    <small>
                                                        <template v-for="(index, user) in entity.attendance">
                                                            <span v-if="user.attended" class="font-grey-silver">@{{ user.name }} (@{{ user.attended | formatTime }})</span>
                                                            <span v-if="index != entity.attendance.length - 1">, </span>
                                                        </template>
                                                    </small>
                                                </td>
                                                <td class="font-red">Not Rostered</td>
                                            </tr>
                                            <tr v-if="entity.open" style="background-color: #444D58" class="nohover">
                                                <td colspan="3" style="padding: 3px 7px 0px 7px">
                                                    <h4 class="font-white">Attendance</h4>
                                                    <table class="table table-striped table-bordered table-hover order-column" style="margin-bottom: 10px">
                                                        <tbody>
                                                        <template v-for="user in entity.attendance">
                                                            <tr>
                                                                <td width="30%" class="font-grey-silver">@{{ user.name }}</td>
                                                                <td>
                                                                    <span v-if="user.attended">@{{ user.attended | formatTime2 }}</span>
                                                                    <span v-if="user.other_sites" class="font-grey-silver"> @{{ user.other_sites }}</span>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                        </tbody>
                                                    </table>

                                                </td>
                                            </tr>
                                        </template>
                                        </tbody>
                                    </table>

                                    <p v-else>No Attendance !</p>
                                </div>
                            </div>

                            <!--<pre v-if="xx.dev">@{{ $data | json }}</pre>
                            -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
@stop


@section('page-level-plugins-head')
@stop

@section('page-level-plugins')
    <script src="/js/moment.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/js/libs/vue.1.0.24.js " type="text/javascript"></script>
<script src="/js/libs/vue-strap.min.js"></script>
<script src="/js/libs/vue-resource.0.7.0.js " type="text/javascript"></script>
<script src="/js/vue-modal-component.js"></script>
<script src="/js/vue-app-planner-functions.js"></script>
<script src="/js/vue-app-planner-attend.js"></script>
@stop