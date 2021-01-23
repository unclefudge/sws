<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Site Maintenance Executive Report</title>
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
    <div class="page22">
        <?php $row_count = 0; ?>
        <?php $page_count = 1; ?>
            @foreach ($mains as $main)
                @if ($row_count == 0)
                    {{-- New Page - Show header --}}
                    <div class="row">
                        <div class="col-xs-9"><h3 style="margin: 0px">Site Maintenance Executive Report ({{ $main->count() }})</h3></div>
                        <div class="col-xs-3"><h6>{{ $from->format('d/m/Y') }}</h6></div>
                    </div>
                    <hr style="margin: 5px 0px">
                    <div class="row">
                        <div class="col-xs-1">Site</div>
                        <div class="col-xs-3">Name</div>
                        <div class="col-xs-2">Category</div>
                        <div class="col-xs-2">Task Owner</div>
                        <div class="col-xs-1">Reported Date</div>
                        <div class="col-xs-1">Allocated Date</div>
                        <div class="col-xs-1">Completed</div>
                    </div>
                    <hr style="margin: 5px 0px">
                    <?php $row_count++; ?>
                @endif


                    <?php
                    $row_count ++;
                    ?>
                    <div class="row">
                        <div class="col-xs-1">{{ $main->site->code }}</div>
                        <div class="col-xs-3">{{ $main->site->name }}</div>
                        <div class="col-xs-2">{{ ($main->category_id) ? \App\Models\Site\SiteMaintenanceCategory::find($main->category_id)->name : '-' }}</div>
                        <div class="col-xs-2">{{ ($main->super_id) ? $main->taskOwner->name : 'Unassigned' }}</div>
                        <div class="col-xs-1">{{ $main->reported->format('d/m/Y') }}</div>
                        <div class="col-xs-1">Allocated Date</div>
                        <div class="col-xs-1">{{ ($main->status) ? 'Active' : $main->updated_at->format('d/m/Y') }}</div>
                    </div>

                @if ($row_count > 28) {{-- New Page if no of lines exceed max --}}
                <div class="page"></div>
                <?php $row_count = 0; $page_count ++ ?>
                @endif
            @endforeach
    </div>
</div>
</body>
</html>