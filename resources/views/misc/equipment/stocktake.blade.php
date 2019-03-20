@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><a href="/equipment">Equipment Allocation</a><i class="fa fa-circle"></i></li>
        <li><span>Stocktake</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-green-haze bold uppercase">Equipment Stocktake</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('stocktake', ['method' => 'PATCH', 'action' => ['Misc\EquipmentStocktakeController@update', ($location) ? $location->id : '0'], 'class' => 'horizontal-form']) !!}
                        {!! Form::hidden('site_id', ($location) ? $location->site_id : null, ['class' => 'control-label', 'id' => 'site_id']) !!}
                        @include('form-error')

                        <div class="form-body">
                            <div class="row">
                                @if ($location)
                                    <div class="col-md-6"><h3>{!! $location->name !!}</h3></div>
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('location_id', $errors) !!}">
                                        {!! Form::label('location_id', 'Change Location', ['class' => 'control-label']) !!}
                                        <select id="location_id" name="location_id" class="form-control select2" width="100%">
                                            <option></option>
                                            <optgroup label="Sites"></optgroup>
                                            @foreach ($sites as $id => $name)
                                                <option value="{{ $id }}" {{ ($location && $location->id == $id) ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                            <optgroup label="Other Locations"></optgroup>
                                            @foreach ($others as $id => $name)
                                                <option value="{{ $id }}" {{ ($location && $location->id == $id) ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        {!! fieldErrorMessage('location_id', $errors) !!}
                                    </div>
                                </div>
                            </div>

                            @if ($location && $location->site_id != 25)
                                <div class="row" id="exclude-div">
                                    <div class="col-md-12">
                                        <button class="btn dark pull-right" id="btn-exclude">Exclude some items from stocktake</button>
                                        <br><br><br>
                                    </div>
                                </div>
                            @endif

                            {{-- Equipment --}}
                            @if ($location)
                                <div id="equipment_list">
                                    {{-- General Equipment --}}
                                    <div class="panel-group accordion scrollable" id="accordion3">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_1" aria-expanded="true"> General </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_1" class="panel-collapse collapse" aria-expanded="true" style="">
                                                <div class="panel-body">
                                                    <table class="table table-striped table-bordered table-hover order-column">
                                                        <thead>
                                                        <tr class="mytable-header">
                                                            <th> Item Name</th>
                                                            <th width="10%"> Expected</th>
                                                            @if (Auth::user()->allowed2('edit.equipment.stocktake', $location))
                                                                <th width="10%"> Actual</th>
                                                                <th width="5%" class="excludeitems"> {!! ($location->site_id == 25) ? 'Include' : 'Exclude' !!}</th>
                                                            @endif
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if (count($items))
                                                            <?php $x = 0; ?>
                                                            @foreach($items->sortBy('item_name') as $loc)
                                                                @if ($loc->equipment->category_id == 1)
                                                                    <?php $x ++; ?>
                                                                    <tr class="itemrow-" id="itemrow-{{ $loc->id }}">
                                                                        <td>{{ $loc->item_name }}</td>
                                                                        <td>{{ $loc->qty }}</td>
                                                                        @if (Auth::user()->allowed2('edit.equipment.stocktake', $location))
                                                                            <td>
                                                                                <div class="itemactual-" id="itemactual-{{ $loc->id }}">
                                                                                    <select id="{{ $loc->id }}-qty" name="{{ $loc->id }}-qty" class="form-control bs-select" width="100%">
                                                                                        @for ($i = 0; $i < 100; $i++)
                                                                                            <option value="{{ $i }}" @if ($i == $loc->qty) selected @endif>{{ $i }}</option>
                                                                                        @endfor
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td class="excludeitems">
                                                                                <div class="text-center">
                                                                                    <label class="mt-checkbox mt-checkbox-outline">
                                                                                        <input type="checkbox" value="{{ $loc->id }}" name="exclude[]" id="itemcheck-{{ $loc->id }}" class="stockitem">
                                                                                        <span></span>
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                        @endif
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        @if (count($items) < 1 || $x < 1)
                                                            <tr>
                                                                <td colspan="4">No items found at current loation.</td>
                                                            </tr>
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Materials Equipment --}}
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2" aria-expanded="false"> Materials (under development) </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_2" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                                <div class="panel-body" style="height:200px; overflow-y:auto;">
                                                    <table class="table table-striped table-bordered table-hover order-column">
                                                        <thead>
                                                        <tr class="mytable-header">
                                                            <th> Item Name</th>
                                                            <th width="10%"> Expected</th>
                                                            @if (Auth::user()->allowed2('edit.equipment.stocktake', $location))
                                                                <th width="10%"> Actual</th>
                                                                <th width="5%" class="excludeitems"> {!! ($location->site_id == 25) ? 'Include' : 'Exclude' !!}</th>
                                                            @endif
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if (count($items))
                                                            <?php $x = 0; ?>
                                                            @foreach($items->sortBy('item_name') as $loc)
                                                                @if ($loc->equipment->category_id == 3)
                                                                    <?php $x ++; ?>
                                                                    <tr class="itemrow-" id="itemrow-{{ $loc->id }}">
                                                                        <td>{{ $loc->item_name }}</td>
                                                                        <td>{{ $loc->qty }}</td>
                                                                        @if (Auth::user()->allowed2('edit.equipment.stocktake', $location))
                                                                            <td>
                                                                                <div class="itemactual-" id="itemactual-{{ $loc->id }}">
                                                                                    <select id="{{ $loc->id }}-qty" name="{{ $loc->id }}-qty" class="form-control bs-select" width="100%">
                                                                                        @for ($i = 0; $i < 100; $i++)
                                                                                            <option value="{{ $i }}" @if ($i == $loc->qty) selected @endif>{{ $i }}</option>
                                                                                        @endfor
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td class="excludeitems">
                                                                                <div class="text-center">
                                                                                    <label class="mt-checkbox mt-checkbox-outline">
                                                                                        <input type="checkbox" value="{{ $loc->id }}" name="exclude[]" id="itemcheck-{{ $loc->id }}" class="stockitem">
                                                                                        <span></span>
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                        @endif
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        @if (count($items) < 1 || $x < 1)
                                                            <tr>
                                                                <td colspan="4">No items found at current loation.</td>
                                                            </tr>
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Scaffold Equipment --}}
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_3" aria-expanded="false"> Scaffold </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_3" class="panel-collapse collapse" aria-expanded="false">
                                                <div class="panel-body">
                                                    <table class="table table-striped table-bordered table-hover order-column">
                                                        <thead>
                                                        <tr class="mytable-header">
                                                            <th> Item Name</th>
                                                            <th width="10%"> Expected</th>
                                                            @if (Auth::user()->allowed2('edit.equipment.stocktake', $location))
                                                                <th width="10%"> Actual</th>
                                                                <th width="5%" class="excludeitems"> {!! ($location->site_id == 25) ? 'Include' : 'Exclude' !!}</th>
                                                            @endif
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if (count($items))
                                                            <?php $x = 0; ?>
                                                            @foreach($items->sortBy('item_name') as $loc)
                                                                @if ($loc->equipment->category_id == 2)
                                                                    <?php $x ++; ?>
                                                                    <tr class="itemrow-" id="itemrow-{{ $loc->id }}">
                                                                        <td>{{ $loc->item_name }}</td>
                                                                        <td>{{ $loc->qty }}</td>
                                                                        @if (Auth::user()->allowed2('edit.equipment.stocktake', $location))
                                                                            <td>
                                                                                <div class="itemactual-" id="itemactual-{{ $loc->id }}">
                                                                                    <select id="{{ $loc->id }}-qty" name="{{ $loc->id }}-qty" class="form-control bs-select" width="100%">
                                                                                        @for ($i = 0; $i < 100; $i++)
                                                                                            <option value="{{ $i }}" @if ($i == $loc->qty) selected @endif>{{ $i }}</option>
                                                                                        @endfor
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td class="excludeitems">
                                                                                <div class="text-center">
                                                                                    <label class="mt-checkbox mt-checkbox-outline">
                                                                                        <input type="checkbox" value="{{ $loc->id }}" name="exclude[]" id="itemcheck-{{ $loc->id }}" class="stockitem">
                                                                                        <span></span>
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                        @endif
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        @if (count($items) < 1 || $x < 1)
                                                            <tr>
                                                                <td colspan="4">No items found at current loation.</td>
                                                            </tr>
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    {{-- Additional --}}
                                    @if (Auth::user()->allowed2('edit.equipment.stocktake', $location))
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button class="btn blue" id="btn-add-item">Additional equipment at location</button>
                                                <br><br>
                                            </div>
                                        </div>


                                        {{-- Additional items --}}
                                        <div style="display: none" id="add-items">
                                            <h3>Additional Items</h3>
                                            <table class="table table-striped table-bordered table-hover order-column">
                                                <thead>
                                                <tr class="mytable-header">
                                                    <th> Item Name</th>
                                                    <th width="10%"> Expected</th>
                                                    @if (Auth::user()->allowed2('edit.equipment.stocktake', $location))
                                                        <th width="10%"> Actual</th>
                                                        <th width="5%" class="excludeitems"> {!! ($location->site_id == 25) ? 'Include' : 'Exclude' !!}</th>
                                                    @endif
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $max = ($location->site_id && $location->site_id == 25) ? 10 : 3;
                                                if ($items)
                                                    $equipment_list = \App\Models\Misc\Equipment\Equipment::where('status', 1)->where('company_id', Auth::user()->company_id)->whereNotIn('id', $items->pluck('equipment_id')->toArray())->get();
                                                else
                                                    $equipment_list = \App\Models\Misc\Equipment\Equipment::where('status', 1)->where('company_id', Auth::user()->company_id)->toArray()->get();

                                                ?>
                                                @for ($x = 1; $x <= $max; $x++)
                                                    <tr class="add-item" style="display: none">
                                                        <td colspan="2">
                                                            <div class="form-group {!! fieldHasError("$x-extra_id", $errors) !!}">
                                                                <select id="{{ $x }}-extra_id" name="{{ $x }}-extra_id" class="form-control select2 sel_add_item" width="100%">
                                                                    <option value="">Add additional item</option>
                                                                    @foreach ($equipment_list as $item)
                                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                {!! fieldErrorMessage("$x-extra_id", $errors) !!}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group {!! fieldHasError($x.'-extra_qty', $errors) !!}">
                                                                <select id="{{ $x }}-extra_qty" name="{{ $x }}-extra_qty" class="form-control bs-select" width="100%">
                                                                    @for ($i = 0; $i < 100; $i++)
                                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                                {!! fieldErrorMessage($x.'-extra_qty', $errors) !!}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>



                            @endif
                        </div>

                        <div class="form-actions right">
                            <a href="/equipment/inventory" class="btn default"> Back</a>
                            @if ($location && Auth::user()->allowed2('edit.equipment.stocktake', $location))
                                <button type="submit" name="save" value="save" class="btn green">Save</button>
                            @endif
                        </div>
                    </div>
                    {!! Form::close() !!}

                    {{-- History --}}
                    @if ($location)
                        <h3 class="form-section">History</h3>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_history">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th width="10%"> Date</th>
                                <th> By Whom</th>
                                <th> Summary</th>
                            </tr>
                            </thead>
                        </table>
                    @endif
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="loadSpinnerOverlay" id="spinner" style="display: none">
                            <div class="loadSpinner"><i class="fa fa-spinner fa-pulse fa-2x fa-fw margin-bottom"></i> Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
    </div>
@stop

@section('page-level-plugins-head')
    <link href="/assets/vendors/base/vendors.bundle.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/demo/default/base/style.bundle.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        /* Select2 */
        $("#location_id").select2({placeholder: "Select location", width: '100%'});
        $(".sel_add_item").select2({placeholder: "Add additional item", width: '100%'});
        // Cape Cod Store by default has all items excluded
        if ($("#site_id").val() == 25) {
            $(".itemrow-").addClass("font-grey-cascade");
            $(".itemactual-").hide();
        } else {
            $(".excludeitems").hide();
        }

        // Add extra items
        $("#btn-add-item").click(function (e) {
            e.preventDefault();
            $("#add-items").show();
            $(".add-item").show();
            $("#btn-add-item").hide();
        });

        // Exclude some items
        $("#btn-exclude").click(function (e) {
            e.preventDefault();
            $(".excludeitems").show();
            $("#exclude-div").hide();
        });

        // Location
        $("#location_id").change(function () {
            $("#equipment_list").hide();
            $("#btn-add-item").hide();
            $("#spinner").show();
            window.location.href = "/equipment/stocktake/" + $("#location_id").val();
        });


        $(".stockitem").click(function (e) {
            if ($("#site_id").val() == 25) {
                // Cape Cod Store by default has all items excluded
                if ($("#itemcheck-" + $(this).val()).prop('checked')) {
                    $("#itemrow-" + $(this).val()).removeClass("font-grey-cascade");
                    $("#itemactual-" + $(this).val()).show();
                } else {
                    $("#itemactual-" + $(this).val()).hide();
                    $("#itemrow-" + $(this).val()).addClass("font-grey-cascade");
                }
            } else {
                // All other location by default has all items included
                if ($("#itemcheck-" + $(this).val()).prop('checked')) {
                    $("#itemactual-" + $(this).val()).hide();
                    $("#itemrow-" + $(this).val()).addClass("font-grey-cascade");
                } else {
                    $("#itemrow-" + $(this).val()).removeClass("font-grey-cascade");
                    $("#itemactual-" + $(this).val()).show();
                }
            }
        });

        var table_history = $('#table_history').DataTable({
            pageLength: 10,
            processing: true,
            serverSide: true,
            ajax: {
                'url': '{!! url('equipment/stocktake/dt/stocktake') !!}',
                'type': 'GET',
                'data': function (d) {
                    d.location_id = "{{ ($location) ? $location->id : 0 }}";
                }
            },
            columns: [
                {data: 'id', name: 'id', orderable: false, searchable: false},
                {data: 'created_at', name: 'created_at'},
                {data: 'created_by', name: 'created_by'},
                {data: 'summary', name: 'summary'},
            ],
            order: [
                [1, "desc"]
            ]
        });
    });
</script>
@stop