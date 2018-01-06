<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Site Plan</title>
    <link href="{{ asset('/') }}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('/') }}/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <style>
        @import url(http://fonts.googleapis.com/css?family=PT+Sans);
        /*@import url(https://fonts.googleapis.com/css?family=Martel+Sans);*/

        @page {
            margin: .7cm .7cm
        }

        body, h1, h2, h3, h4, h5, h6 {
            font-family: 'PT Sans', serif;
        }

        h1 {
            /*font-family: 'Martel Sans', sans-serif;*/
            font-weight: 700;
        }

        body {
            font-size: 10px;
        }

        div.page {
            page-break-after: always;
            page-break-inside: avoid;
        }

        .row-striped:nth-of-type(odd) {
            background-color: #ffffff;
        }

        .row-striped:nth-of-type(even) {
            background-color: #f4f4f4;
        }

        .border-right {
            border-right: 1px solid lightgrey;
            margin-bottom: -999px;
            padding-bottom: 999px;
        }
    </style>
</head>

<body>
<div class="container">
    @foreach($sitedata as $siteplan)
        <div class="page22">
            <?php $site = App\Models\Site\Site::find($siteplan->site_id) ?>
            <div class="row">
                <div class="col-xs-8">
                    <h3 style="margin: 0px">{{ $site->name }}
                        <small>site: {{ $site->code }}</small>
                    </h3>{{ $site->address }}, {{  $site->suburb_state_postcode }}</div>
                <div class="col-xs-4">
                    <h6><b>Supervisor:</b> {{ $site->supervisorsSBC() }}</h6>
                </div>
            </div>
            <hr style="margin: 5px 0px">
            @foreach($siteplan->weeks as $week_num => $week_data )
                <div class="row">
                    <div class="col-xs-12"><h6 style="margin: 0px 0px 5px 0px"><b>Week {{ $week_num }}</b></h6></div>
                </div>
                <table class="table table-striped table-bordered table-hover order-column" style="padding: 0px; margin: 0px">
                    @foreach($week_data as $row )
                        @if($row[0] == 'COMPANY')
                            <thead>
                            <tr style="background-color: #f0f6fa; font-weight: bold;">
                                <th width="16%">{!! $row[0] !!}</th>
                                <th width="16%">{!! $row[1] !!}</th>
                                <th width="16%">{!! $row[2] !!}</th>
                                <th width="16%">{!! $row[3] !!}</th>
                                <th width="16%">{!! $row[4] !!}</th>
                                <th width="16%">{!! $row[5] !!}</th>
                            </tr>
                            </thead>
                        @else
                            <tr>
                                @if($row[0] == 'NOTHING-ON-PLAN')
                                    <td colspan="6">No tasks for this week</td>
                                @else
                                    <td width="16%">{!! $row[0] !!}</td>
                                    <td width="16%">{!! $row[1] !!}</td>
                                    <td width="16%">{!! $row[2] !!}</td>
                                    <td width="16%">{!! $row[3] !!}</td>
                                    <td width="16%">{!! $row[4] !!}</td>
                                    <td width="16%">{!! $row[5] !!}</td>
                                @endif
                            </tr>
                        @endif
                    @endforeach
                </table>
                <br>
            @endforeach
        </div>
    @endforeach
</div>
</body>
</html>