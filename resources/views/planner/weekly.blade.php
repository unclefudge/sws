@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-calendar"></i> Weekly Planner</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><span>Weekly Planner</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        <input v-model="xx.mon_now" type="hidden" value="{{ $date }}">
        <input v-model="xx.params.date" type="hidden" value="{{ $date }}">
        <input v-model="xx.params.supervisor_id" type="hidden" value="{{ $supervisor_id }}">
        <input v-model="xx.params.site_id" type="hidden" value="{{ $site_id }}">
        <input v-model="xx.params.site_start" type="hidden" value="{{ $site_start }}">
        <input v-model="xx.user_company_id" type="hidden" value="{{ Auth::user()->company_id }}">
        <input v-model="xx.show_contact" type="hidden" value="{{ (Auth::user()->company->parent_company) ? '1': '0' }}">
        {{-- Extended Weekly view for trades
        13-Bricklayer, 4-Electrician, 5-Floor and Wall Tiler, 6-Gyprocker, 7-Painter, 8-Plumber, 9-Roofer, 20-Roof Plumber, 27-Renderer, 11-Stairs --}}
        <input v-model="xx.plan_ahead" type="hidden" value="{{ (!empty(array_intersect([13, 4, 5, 6, 7, 8, 9, 20, 27, 11], Auth::user()->company->tradesSkilledIn->pluck('id')->toArray()))) ? '48': '20' }}">
        @if (Auth::user()->company->parent_company && Auth::user()->company->reportsTo()->id == 3)
            <div class="note note-warning">
                This is a guide only. Contact with Site Supervisor is still required.
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Weekly Planner</span>
                        </div>
                        <div class="actions">
                            @if (Auth::user()->hasPermission2('view.trade.planner'))
                                <button v-on:click="gotoURL('/planner/transient')" class="btn btn-circle btn-icon-only btn-default" style="margin: 3px">L</button>
                            @endif
                            @if (Auth::user()->hasPermission2('view.roster'))
                                <button v-on:click="gotoURL('/planner/roster')" class="btn btn-circle btn-icon-only btn-default" style="margin: 3px">R</button>
                            @endif
                            @if (Auth::user()->hasPermission2('view.site.planner'))
                                <button v-on:click="gotoURL('/planner/site')" class="btn btn-circle btn-icon-only btn-default" style="margin: 3px">S</button>
                            @endif
                            @if (Auth::user()->hasPermission2('view.trade.planner'))
                                <button v-on:click="gotoURL('/planner/trade')" class="btn btn-circle btn-icon-only btn-default" style="margin: 3px">T</button>
                            @endif
                            <button class="btn btn-circle btn-icon-only grey-steel disabled" style="margin: 3px">W</button>
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen" style="margin: 3px"></a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row" style="padding-bottom: 5px">
                            <div class="col-md-3">
                                {!! Form::select('supervisor_id', $supervisors, $supervisor_id, ['class' => 'form-control bs-select', 'id' => 'supervisor_id',]) !!}
                            </div>
                            <div class="col-md-5 text-center"><h4 class="bold font-green-haze">@{{ weeklyHeader(xx.mon_now, 0) }}</h4></div>
                            <div class="col-md-4 pull-right">
                                <div class="btn-group btn-group-circle pull-right">
                                    @if(true || Auth::user()->company->subscription)
                                        <button v-on:click="changeWeek(weekDate(xx.mon_now, -7))" class="btn blue-hoki">Prev Week</button>
                                    @endif
                                    <button v-on:click="changeWeek(weekDate(xx.mon_this, 0))" class="btn blue-dark">This Week</button>
                                    <button v-if="viewWeek(weekDate(xx.mon_now, 7))" v-on:click="changeWeek(weekDate(xx.mon_now, 7))" class="btn blue-hoki">Next Week</button>
                                </div>
                            </div>
                        </div>
                        <!--<app-weekly></app-weekly>-->
                        <div v-show="xx.sites.length">
                            <div class="row" style="background-color: #f0f6fa; font-weight: bold; min-height: 40px; display: flex; align-items: center;">
                                <div class="col-xs-2">Site</div>
                                <div class="col-xs-2">Mon @{{ weekDateHeader(xx.mon_now, 0) }}</div>
                                <div class="col-xs-2">Tue @{{ weekDateHeader(xx.mon_now, 1) }}</div>
                                <div class="col-xs-2">Wed @{{ weekDateHeader(xx.mon_now, 2) }}</div>
                                <div class="col-xs-2">Thu @{{ weekDateHeader(xx.mon_now, 3) }}</div>
                                <div class="col-xs-2">Fri @{{ weekDateHeader(xx.mon_now, 4) }}</div>
                            </div>
                            <template v-for="site in xx.sites">
                                <app-site :site_id="site.id" :site_name="site.name" :site_code="site.code" :site_contact="site.supervisors_contact" :site_address="site.address"></app-site>
                            </template>

                        </div>
                    </div>
                </div>

                <!--<pre v-if="xx.dev">@{{ $data | json }}</pre>
                -->

            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->

    <!-- loading Spinner -->
    <div v-show="xx.load_plan" style="background-color: #FFF; padding: 20px;">
        <div class="loadSpinnerOverlay">
            <div class="loadSpinner"><i class="fa fa-spinner fa-pulse fa-2x fa-fw margin-bottom"></i> Loading...</div>
        </div>
    </div>

    <!--<template id="weekly-template"></template> -->


    <template id="site-template">
        <div v-show="showSite(site_id) && site_code != '0007'" class="row row-striped" style="border-bottom: 1px solid lightgrey; overflow: hidden;">
            <div class="col-xs-2 sideColBG">
                <small>@{{ site_name | max20chars }}<br>
                    <small>
                        <span v-if="xx.show_contact == 1"><br><span v-html="site_address"></span><br>@{{ site_contact }}</span>
                        <span v-else>@{{ site_code }}</span>
                    </small>
                </small>
            </div>
            <div class="col-xs-2" v-bind:class="{ 'todayBG': weekDate(xx.mon_now, 0 ) == xx.today }">
                <app-dayplan :date="weekDate(xx.mon_now, 0)" :site_id="site_id"></app-dayplan>
            </div>
            <div class="col-xs-2" v-bind:class="{ 'todayBG': weekDate(xx.mon_now, 1 ) == xx.today }">
                <app-dayplan :date="weekDate(xx.mon_now, 1)" :site_id="site_id"></app-dayplan>
            </div>
            <div class="col-xs-2" v-bind:class="{ 'todayBG': weekDate(xx.mon_now, 2 ) == xx.today }">
                <app-dayplan :date="weekDate(xx.mon_now, 2)" :site_id="site_id"></app-dayplan>
            </div>
            <div class="col-xs-2" v-bind:class="{ 'todayBG': weekDate(xx.mon_now, 3 ) == xx.today }">
                <app-dayplan :date="weekDate(xx.mon_now, 3)" :site_id="site_id"></app-dayplan>
            </div>
            <div class="col-xs-2" v-bind:class="{ 'todayBG': weekDate(xx.mon_now, 4 ) == xx.today }">
                <app-dayplan :date="weekDate(xx.mon_now, 4)" :site_id="site_id"></app-dayplan>
            </div>
        </div>


        <!--<pre v-if="xx.dev">@{{ $data | json }}</pre> -->
    </template>

    <!-- Day plan for each entity on planner -->
    <template id="dayplan-template">
        <!-- Past Events - disable sidebar and dim entry -->
        <div v-show="pastDate(date) == true" style="padding: 10px; opacity: 0.5">
            <div v-if="day_plan.length">
                <template v-for="entity in day_plan">
                    <div class="@{{ entityClass(entity) }}">
                        <small>@{{ entity.entity_name | max10chars }} (@{{{ entity.tasks }}})</small>
                    </div>
                </template>
            </div>
            <!-- Non-rostered -->
            <template v-for="user in non_rostered">
                <div>
                    <small>*@{{ user | max10chars }}</small>
                </div>
            </template>

        </div>
        <!-- Current Events -->
        <div v-else class="hoverDiv" v-on:click="viewSitePlan(site_id)">
            <div v-if="day_plan.length">
                <template v-for="entity in day_plan">
                    <div class="@{{ entityClass(entity) }}">
                        <small>@{{ entity.entity_name | max10chars }} (@{{{ entity.tasks }}})</small>
                    </div>
                </template>
            </div>
            <!-- Non-rostered -->
            <template v-for="user in non_rostered">
                <div>
                    <small class="font-grey-silver">*@{{ user | max10chars }}</small>
                </div>
            </template>
        </div>

        <!-- <pre v-if="xx.dev">@{{ site_id }}<br>@{{ date }}<br>@{{ day_plan | json }}</pre>-->
    </template>
@stop


@section('page-level-plugins-head')
@stop

@section('page-level-plugins')
    <script src="/js/moment.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/js/libs/vue.1.0.24.js " type="text/javascript"></script>
<script src="/js/libs/vue-resource.0.7.0.js " type="text/javascript"></script>
<script src="/js/vue-app-planner-functions.js"></script>
<script src="/js/vue-app-planner-weekly.js"></script>
@stop