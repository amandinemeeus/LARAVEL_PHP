<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: crimson;
                font-family: 'Raleway', sans-serif;
                font-weight: bold;
                height: 100vh;
                margin: 0;
            }

            .numero{
                display:inline-block; 
                width: 40px; 
                height: 40px; 
                text-align: center; 
                margin: 10px;
                border-radius:5px;
            }
            .nom{
                font-weight: bold;
                color: black;
            }

            li {}
            a {text-decoration:none}
            h1 {margin-left: 20px}

        </style>
    </head>
    <body>
        <h1>{{$route->route_short_name}} / {{ $route->route_long_name}} </h1> 
        <ul>
            @foreach ($stops as $stop)

                <li> 
                    {{ $stop->stop_name }}
                    @if (isset($stop->here)) ==> Good vibes :-)
                    @endif
                </li>
                <br>
                
            @endforeach
        </ul>

    </body>
</html>
