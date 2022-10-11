@extends('layout')
@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Safety in Design</span></li>
    </ul>
    @stop

    @section('content')

    <div class="page-content-inner">
        {{-- Reports --}}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze">Safety in Design</span>
                        </div>
                        <div class="actions">
                            @if(Auth::user()->allowed2('add.site.inspection'))
                                <a class="btn btn-circle green btn-outline btn-sm" href="/site/inspection/safetydesign/create" data-original-title="Add">Add</a>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 pull-right">
                            <div class="form-group">
                                <select name="status" id="status" class="form-control bs-select">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover order-column" id="table1">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th> Site</th>
                                <th> Prepared by</th>
                                <th width="10%"> Conducted</th>
                                <th width="10%"> Completed</th>
                                <th width="5%"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script type="text/javascript">
    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name=token]').attr('value')}
    });

    $(document).ready(function () {
        var status = $('#status').val();

        var table1 = $('#table1').DataTable({
            pageLength: 100,
            processing: true,
            serverSide: true,
            ajax: {
                'url': '{!! url('site/inspection/custom/dt/safetydesign') !!}',
                'type': 'GET',
                'data': function (d) {
                    d.status = $('#status').val();
                }
            },
            columns: [
                {data: 'view', name: 'view', orderable: false, searchable: false},
                {data: 'sitename', name: 'sites.name'},
                {data: 'sitename', name: 'sites.name'},
                {data: 'createddate', name: 'forms.created_at'},
                {data: 'updateddate', name: 'forms.updated_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [
                [2, "desc"]
            ]
        });

        $('select#status').change(function () {
            table1.ajax.reload();
        });

        // Warning message for deleting report
        $('.delete-report').click(function (e) {
            e.preventDefault();
            var url = "/site/inspection/plumbing/" + $(this).data('id');
            var name = $(this).data('name');

            swal({
                title: "Are you sure?",
                text: "The report <b>" + name + "</b> will be deleted.<br><br><span class='font-red'><i class='fa fa-warning'></i> You will not be able to undo this action!</span>",
                showCancelButton: true,
                cancelButtonColor: "#555555",
                confirmButtonColor: "#E7505A",
                confirmButtonText: "Yes, delete it!",
                allowOutsideClick: true,
                html: true,
            }, function () {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    data: {method: '_DELETE', submit: true},
                    success: function (data) {
                        toastr.error('Deleted report');
                    },
                }).always(function (data) {
                    location.reload();
                });
            });
        });

        table1.on('click', '.btn-delete[data-id]', function (e) {
            e.preventDefault();
            var url = "/site/inspection/plumbing/" + $(this).data('id');
            var name = $(this).data('name');

            swal({
                title: "Are you sure?",
                text: "The report <b>" + name + "</b> will be deleted.<br><br><span class='font-red'><i class='fa fa-warning'></i> You will not be able to undo this action!</span>",
                showCancelButton: true,
                cancelButtonColor: "#555555",
                confirmButtonColor: "#E7505A",
                confirmButtonText: "Yes, delete it!",
                allowOutsideClick: true,
                html: true,
            }, function () {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    data: {method: '_DELETE', submit: true},
                    success: function (data) {
                        toastr.error('Deleted report');
                    },
                }).always(function (data) {
                    location.reload();
                });
            });
        });

    });

</script>
@stop