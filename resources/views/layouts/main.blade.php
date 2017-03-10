<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>IT24</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @if (Auth::check())
                        @can('uploads')
                        <a href="{{ url('/panel') }}">Панель</a>
                        @elsecan('schedules')
                        <a href="{{ url('/panel/downloads') }}">Панель</a>
                        @endcan
                    @else
                        <a href="{{ url('/login') }}">Вход</a>
                        <a href="{{ url('/register') }}">Регистрация</a>
                    @endif
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Analyze-IT
                </div>
                <div class="links">
                    <a href="#">Документация</a>
                    <a href="#">О нас</a>
                    <a href="#">Поставщикам</a>
                    <a href="#">Клиентам</a>
                    <a href="#">Новости</a>
                </div>
            </div>
        </div>
    </body>
</html>
