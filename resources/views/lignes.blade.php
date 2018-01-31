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
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
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

            li {list-style-type: none}
            a {text-decoration:none}

        </style>
    </head>
    <body>
        <ul>
            @foreach ($routes as $route)
                <li> 
                    <a href="{{ route('show',['id'=> $route -> route_short_name]) }}">
                        <span class="numero" style="background-color:{{ $route -> route_color }}; color:{{$route -> route_text_color}}">{{ $route -> route_short_name }} </span>
                        <span class="nom" >{{ $route -> route_long_name }} </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </body>
</html>
