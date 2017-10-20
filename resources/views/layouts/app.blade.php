<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/it24.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand logo-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        @can('uploads')
                        <li @if(isset($panel)&&($panel=='uploads'))class="active"@endif><a href="/panel">Загрузки поставщиков @if(isset($panel)&&($panel=='uploads'))<span class="sr-only">(current)</span>@endif</a></li>
                        @endcan
                        <li @if(isset($panel)&&($panel=='downloads'))class="active"@endif><a href="/panel/downloads">Статистика выгрузки @if(isset($panel)&&($panel=='downloads'))<span class="sr-only">(current)</span>@endif</a></li>
                        @can('suppliers')
                        <li @if(isset($panel)&&($panel=='suppliers'))class="active"@endif><a href="/panel/suppliers">Поставщики @if(isset($panel)&&($panel=='suppliers'))<span class="sr-only">(current)</span>@endif</a></li>
                        @elsecan('schedules')
                        <li @if(isset($panel)&&($panel=='schedules'))class="active"@endif><a href="/panel/schedules">Настройки загрузки @if(isset($panel)&&($panel=='schedules'))<span class="sr-only">(current)</span>@endif</a></li>
                        @endcan
                        @can('users')
                        <li @if(isset($panel)&&($panel=='users'))class="active"@endif><a href="/panel/users">Клиенты @if(isset($panel)&&($panel=='users'))<span class="sr-only">(current)</span>@endif</a></li>
                        @endcan
                        <!--<li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Action</a></li>
                                <li><a href="#">Another action</a></li>
                                <li><a href="#">Something else here</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="#">Separated link</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="#">One more separated link</a></li>
                            </ul>
                        </li>-->
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Вход</a></li>
                            <li><a href="{{ route('register') }}">Регистрация</a></li>
                        @else
                        @can('suppliers')
                            <li @if(isset($panel)&&($panel=='goods'))class="active"@endif><a href="/panel/catalog">Каталог\Товары @if(isset($panel)&&($panel=='goods'))<span class="sr-only">(current)</span>@endif</a></li>
                            @endcan
                            <li @if(isset($panel)&&($panel=='mygoods'))class="active"@endif><a href="/panel/mygoods">Мой Каталог @if(isset($panel)&&($panel=='mygoods'))<span class="sr-only">(current)</span>@endif</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/profile">Профиль</a></li>
                                    @can('schedules')
                                    <li><a href="/support">Поддержка</a></li>
                                    @endcan
                                    <li><a href="#">О системе</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Выйти
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    @if(isset($panel))
    @include('scripts.'.$panel)
    @endif
    <script src="{{ asset('js/it24.js') }}"></script>
    <div class="modal" id="page_loading">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row" style="text-align:center;">
                        <i class="fa fa-spin fa-2x fa-spinner"></i>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end .modal-->
    <script>
        var pageModalLoadingTimeout =false;
        $( document ).ajaxSend(function() {
            // $( ".log" ).text( "Triggered ajaxSend handler." );
            if( pageModalLoadingTimeout===false)pageModalLoadingTimeout = setTimeout(function(){
                $('#page_loading').modal();
            },1200);

        });
        $( document ).ajaxComplete(function() {
            clearTimeout(pageModalLoadingTimeout);
            pageModalLoadingTimeout=false;
            $('#page_loading').modal('hide');
        });
    </script>
</body>
</html>
