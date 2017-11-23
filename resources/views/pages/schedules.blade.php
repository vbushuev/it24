@extends('layouts.app')

@section('content')
<div class="container">
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
                        <ul class="nav navbar-nav navbar-right">
                            <li>
                                <a href="javascript:{addSchedule();}" class="" role="button" aria-haspopup="true" aria-expanded="false">
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
            <h4>Поставщики</h4>
            <div class="row">
            </div>
            <div class="row heading">
                <div class="col-md-2">Наименование</div>
                <div class="col-md-4">Сервер</div>
                <div class="col-md-4">Товары/Каталоги</div>
                <div class="col-md-1">Периодичность</div>
                <div class="col-md-1"></div>
            </div>

        </div>
        <div id="js-container" class="panel-body" data-ref="/data/schedules"></div>
    </div>

</div>
@endsection
