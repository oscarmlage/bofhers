<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>#BOFHers</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

         <!-- Styles -->
                <style>
                    html, body {
                        background-color: #1d2021;
                        color: #e8e8e8;
                        font-family: 'Nunito', sans-serif;
                        font-weight: 200;
                        height: 100vh;
                        margin: 0;
                    }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            ul#channels {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            ul#channels li {
                /* position: relative;
                list-style-type: none;
                padding-left: 2.5rem;
                margin-bottom: 0.5rem;
                text-align: left; */
                flex: 1 1 25%;
                list-style: none;
                text-align: left;
            }
            ul#channels li a {
                color:inherit;
                text-decoration:none;
            }
            ul#channels li:before {
                content: '';
                display: block;
                position: relative;
                left: -10%;
                top: 11px;
                width: 5px;
                height: 11px;
                border-width: 0 2px 2px 0;
                border-style: solid;
                border-color: #f64f01;
                transform-origin: bottom left;
                transform: rotate(45deg);
            }
            ul#channels > * {
                display: inline;
            }
            ul#channels > *:after {
                content: ",";
            }
            ul#channels > *:last-child:after {
                content: "";
            }

            @media (min-width: 768px) {
                ul#channels {
                    list-style: square;
                    padding-left: 30px;
                }
                ul#channels > * {
                    display: list-item;
                    width: 20%;
                    float: left;
                }
                ul#channels > *:after {
                    content: "";
                }
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    #BOFHers
                </div>
                <ul id="channels">
                    <li><a href="https://t.me/joinchat/AFVS0FRbn4VrpiloQlHz3A">#BOFHers</a></li>
                    <li><a href="https://t.me/joinchat/RVmLC4BGx65ObjnK">#BOFHers Gamers</a></li>
                    <li><a href="https://t.me/joinchat/AFVS0Fcqtx8rsf9KqoyZRA">#BOFHers Help</a></li>
                    <li><a href="https://t.me/bofhers_lawyers">#BOFHers Lawyers</a></li>
                    <li><a href="https://t.me/joinchat/IcvowkWiNEoyfQuyXKZEDg">#BOFHers Makers</a></li>
                    <li><a href="https://t.me/joinchat/TOXRWaXwC-7Y8-HP">#BOFHers Mascotas</a></li>
                    <li><a href="https://t.me/bofhers_news">#BOFHers News</a></li>
                    <li><a href="https://t.me/bofhers_recetas">#BOFHers Recetas</a></li>
                    <li><a href="https://t.me/joinchat/B3olGlsZnkdhMDc0">#BOFHers Streaming</a></li>
                </ul>
            </div>
        </div>
    </body>
</html>
