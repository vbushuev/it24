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
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">Фильтры</a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="javascript:0" class="dropdown-toggle brands" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Бренды <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:{filters.filter.brands();}">Очистить</a></li>
                                    <li role="separator" class="divider"></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="javascript:0" class="dropdown-toggle suppliers" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Поставщики <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:{filters.data.lastId=0;filters.data.supply_id='';pageLoad();}">Очистить</a></li>
                                    <li role="separator" class="divider"></li>
                                </ul>
                            </li>
                        </ul>
                        <form class="navbar-form navbar-left">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="поиск">
                            </div>
                            <button type="submit" class="btn btn-default">Поиск</button>
                        </form>
                    </div>
                    <!-- /.navbar-collapse -->
                </div>
                <!-- /.container-fluid -->
            </nav>

            <h4>Загрузки от поставщиков</h4>
            <div class="row heading">
                <div class="col-md-1"></div>
                <div class="col-md-2">Идентификатор</div>
                <div class="col-md-3">Бренд/Наименование</div>
                <div class="col-md-2">Поставщик</div>
                <div class="col-md-1">Цена</div>
                <div class="col-md-1">min</div>
                <div class="col-md-2">Параметры</div>
            </div>
        </div>
        <div id="js-container" class="panel-body">
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
