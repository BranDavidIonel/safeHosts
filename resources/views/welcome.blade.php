<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosts Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        td span {
            word-wrap: break-word;
            white-space: normal;
            max-width: 150px; /* Ajustează dimensiunea maximă */
            display: block;
        }
    </style>
</head>
<body class="container mt-5">

    <h1 class="mb-4">Hosts Overview</h1>

    <div class="card mb-4 text-center">
        <div class="card-body">
            <h4>Total Custom Hosts: <strong>{{ $totalCustomHosts }}</strong></h4>
            <h4>Total Hosts in Database: <strong>{{ $totalHosts }}</strong></h4>
        </div>
    </div>
    <a href="{{route('custom-list.index')}}" class="btn btn-primary mb-3">Manage Custom Users</a>
    <div class="row">
        <!-- Custom User Hosts -->
        <div class="col-md-6">
            <h2>The latest 10 custom hosts</h2>
            <table class="table table-bordered">
                <thead class="table-info">
                <tr>
                    <th>#</th>
                    <th>Domain</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($customHosts as $index => $custom)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $custom->host->domain }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- All Hosts -->
        <div class="col-md-6">
            <h2>The latest 10 hosts imported</h2>
            <table class="table table-bordered">
                <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Domain</th>
                    <th>Source</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($hosts as $index => $host)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $host->domain }}</td>
                        <td>
                            <span title="{{$host->source}}">{{ $host->source}}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
