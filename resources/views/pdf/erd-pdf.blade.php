<!DOCTYPE html>
<html>
<head>
    <title>ERD PDF</title>

    <style>

        body{
            font-family:Arial, sans-serif;
        }

        h1{
            text-align:center;
            margin-bottom:30px;
        }

        .table-box{
            border:1px solid #000;
            padding:15px;
            margin-bottom:20px;
            border-radius:10px;
        }

        .table-title{
            font-size:20px;
            margin-bottom:10px;
            font-weight:bold;
        }

        ul{
            padding-left:20px;
        }

    </style>

</head>

<body>

    <h1>Laravel ERD Diagram</h1>

    @foreach($tables as $table)

        <div class="table-box">

            <div class="table-title">
                {{ $table['name'] }}
            </div>

            <ul>

                @foreach($table['columns'] as $column)

                    <li>{{ $column }}</li>

                @endforeach

            </ul>

        </div>

    @endforeach

</body>
</html>