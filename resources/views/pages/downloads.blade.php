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
                        <a class="navbar-brand" href="#">Фильтры</a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="filters-navbar-collapse">
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="javascript:0" class="dropdown-toggle clients" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Клиенты <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:{page.filters.data.f=0;page.filters.data.client_id='';page.load();}">Очистить</a></li>
                                    <li role="separator" class="divider"></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:0" class="" role="button" aria-haspopup="true" aria-expanded="false">
                                    <input type="date" name="date" placeholder="Дата" onchange="{page.filters.data.f=0;page.filters.data.date=$(this).val();page.load();}"/>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:{page.filters.filter.error();}" class="" role="button" aria-haspopup="true" aria-expanded="false">
                                    C ошибками
                                </a>
                            </li>
                        </ul>
                        <!--<form class="navbar-form navbar-left">
                            <div class="form-group">
                                <input type="text" class="form-control search" placeholder="поиск" onchange="{page.filters.search($(this));}" name="search">
                            </div>
                        </form>-->
                        <ul class="nav navbar-nav navbar-right">
                            <li>
                                <a href="javascript:page.filters.clear();">Сбросить фильтры</a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.navbar-collapse -->
                </div>
                <!-- /.container-fluid -->
            </nav>
            <h4>Выгрузки клиентам</h4>
            <div class="row heading">
                <div class="col-md-1">#</div>
                <div class="col-md-2">Cтатус</div>
                <div class="col-md-2">Клиент</div>
                <div class="col-md-2">Дата</div>
                <div class="col-md-2">Начало</div>
                <div class="col-md-2">Сумма</div>
                <div class="col-md-1">Кол-во записей</div>
            </div>
        </div>
        <div id="js-container" class="panel-body" data-ref="/data/downloads">
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
