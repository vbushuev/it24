@extends('layouts.app')

@section('content')
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#filters-panel" aria-expanded="false">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">Фильтры</a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="filters-panel">
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="javascript:0" class="dropdown-toggle catalogs" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Категории <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:{page.filters.clear('catalog_id');page.load();}">Очистить</a></li>
                                    <li role="separator" class="divider"></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="javascript:0" class="dropdown-toggle brands" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Бренды <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:{page.filters.clear('brand_id');page.load();}">Очистить</a></li>
                                    <li role="separator" class="divider"></li>
                                </ul>
                            </li>
                            @can('uploads')
                            <li class="dropdown">
                                <a href="javascript:0" class="dropdown-toggle suppliers" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Поставщики <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:{page.filters.clear('supply_id');page.load();}">Очистить</a></li>
                                    <li role="separator" class="divider"></li>
                                </ul>
                            </li>
                            @endcan
                        </ul>
                        <form class="navbar-form navbar-left" role="search">
                            <div class="form-group">
                                <input type="text" class="form-control search" placeholder="поиск" onkeyup="{page.filters.search($(this));}" name="search">
                            </div>
                        </form>
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

            <h4>Товары</h4>
            <!-- <div class="row heading">
                <div class="col-md-1"></div>
                <div class="col-md-2">Идентификатор</div>
                <div class="col-md-3">Бренд/Наименование</div>
                <div class="col-md-2">Поставщик</div>
                <div class="col-md-1">Цена</div>
                <div class="col-md-1">min</div>
                <div class="col-md-2">Параметры</div>
            </div> -->
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4 js-container catalog-container" data-ref="/data/catalogs" data-func="catalogLoader"></div>
                <div class="col-md-8 js-container goods-container" data-ref="/data/goods" data-func="goodsLoader"></div>
            </div>

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
