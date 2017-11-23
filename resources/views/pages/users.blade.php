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
                        <ul class="nav navbar-nav">
                            <li>
                                <a href="javascript:page.filters.clear();">Сбросить</a>
                            </li>
                            <li class="dropdown">
                                <a href="javascript:0" class="dropdown-toggle roles" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Роль <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:{page.filters.clear('role');}">Очистить</a></li>
                                    <li role="separator" class="divider"></li>
                                </ul>
                            </li>
                        </ul>
                        <form class="navbar-form navbar-left">
                            <div class="form-group">
                                <input type="text" class="form-control search" placeholder="поиск" onkeyup="{page.filters.searchName($(this));}" name="search">
                            </div>
                        </form>
                        <ul class="nav navbar-nav navbar-right">
                            <li>
                                <a href="javascript:{user.add();}" class="" role="button" aria-haspopup="true" aria-expanded="false">
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
            <h4>Список клиентов</h4>
            <div class="row heading">
                <div class="col-md-1"></div>
                <div class="col-md-3">Имя пользователя</div>
                <div class="col-md-2">Почта</div>
                <div class="col-md-2">Зарегистрирован</div>
                <div class="col-md-2">Роль</div>
                <div class="col-md-2"></div>
            </div>
        </div>
        <div id="js-container" class="panel-body" data-ref="/data/users">
        </div>
    </div>

</div>
@endsection
