<!DOCTYPE html>
<html>

<head>
<title>
        Attendance
</title>
    <meta name="viewport" content="width=device-width" />

    <link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap.min.css')}}">
    <script src="{{asset('theme/js/jquery.min.js')}}"></script>
    <script src="{{asset('theme/js/bootstrap.min.js')}}"></script>

    <title>Form 1</title>

    <style type="text/css">
        ul {
            list-style: none;
        }

        ul li.arrow-li:before {
            content: "\00BB \0020";
            margin-left: -12px;
        }
        p,td,span{
            font-size: 10px;
        }
        h5,h6{
            font-size: 11px;
        }
        .row{
            margin-left: 0px;
            margin-right: 0px;
        }
        td{
            padding: 3px !important;
        }
    </style>

</head>
<body>
    <center>
    <div class="container" style="background: white; width: 700px; padding-top: 5px">
        <div class ="row">
            <div class ="col-md-12">
                <table class ="table table-responsive">
                    <thead>
                        <th>Name</th>
                        <th>Remarks</th>
                    </thead>
                    <tbody>

                            @forelse($campaign->attendance as $attendance)
                            <tr>
                                <td>{{$attendance->user->name()}}</td>
                                <td>{{$attendance->remarks}}</td>
                            </tr>
                            @empty
                            <tr>
                            <td></td>
                            <td></td>
                            </tr>
                            @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>  
    </center>    
</body>
</html>