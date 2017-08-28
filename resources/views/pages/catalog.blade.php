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
                        <a class="navbar-brand" href="#"></a>
                    </div>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="filters-navbar-collapse">
                        <form class="navbar-form navbar-left">
                            <input type="hidden" name="parent_id" />
                            <div class="form-group">
                                <input type="text" class="form-control search" placeholder="поиск" onkeyup="{page.filters.searchName($(this));}" name="search">
                            </div>
                        </form>
                        <ul class="nav navbar-nav navbar-right">
                            <li>
                                <a href="javascript:{$('#catalogs').modal();}" class="" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-eye"></i> Посмотреть
                                </a>
                            </li>
                            <li>
                                <a href="javascript:{catalog.add();}" class="" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-plus"></i> Добавить
                                </a>
                            </li>
                            <!--<li><a href="javascript:page.filters.clear();">Сбросить фильтры</a></li>-->
                        </ul>
                    </div>
                    <!-- /.navbar-collapse -->
                </div>
                <!-- /.container-fluid -->
            </nav>
            <h4>Каталог товаров</h4>

        </div>
        <div class="panel-body">
            <!-- <div id="js-container" class="col-md-4" data-ref="/data/catalogs"></div> -->
            <div class="col-md-4 js-container catalog-container" data-ref="/data/catalogs" data-func="_contentLoader" data-scroll="false" data-sort="alphabetic"></div>
            <div id="pageGoods" class="col-md-8 js-container goods-container-2" data-ref="/data/goodpage" data-func="goodsLoader" data-auto="false" data-scroll="false" data-paging="true"></div>
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
