@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @include('navbar')
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#filters-navbar-collapse" aria-expanded="false">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                        </button>
                        <!-- <a class="navbar-brand" href="#">Фильтры</a> -->
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="filters-navbar-collapse">
                        <ul class="nav navbar-nav  navbar-right">
                            <li>
                                <a href="javascript:{$.get('/command/uploads',function(d){console.debug(d);});}" class="" role="button" aria-haspopup="true" aria-expanded="false">
                                    Принудительная выгрузка
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.navbar-collapse -->
                </div>
                <!-- /.container-fluid -->
            </nav>

            <h4>Поставщики</h4>
            <div class="row heading">
                <div class="col-md-2">Наименование</div>
                <div class="col-md-3">Протокол/ссылка</div>
                <div class="col-md-2">ИНН</div>
                <div class="col-md-1">Наценка</div>
                <div class="col-md-2">Дата последней загрузки</div>
                <div class="col-md-1">Периодичность</div>
                <div class="col-md-1"></div>
            </div>
        </div>
        <div id="js-container" class="panel-body" data-ref="/data/suppliers">
        </div>
    </div>
    <!--<div class="row">
        <div class="col-md-10 col-md-offset-1">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Мониторинг загрузки от поставщиков</div>

                <div id="js-container" class="panel-body">
                </div>

            </div>
        </div>
    </div>-->
</div>
@endsection
