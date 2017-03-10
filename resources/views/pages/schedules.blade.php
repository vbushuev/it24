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
                <div class="col-md-4">Товары</div>
                <div class="col-md-1">Периодичность</div>
                <div class="col-md-1"></div>
            </div>

        </div>
        <div id="js-container" class="panel-body" data-ref="/data/schedules">
        </div>
    </div>
    <div class="modal fade" id="add_schedule" data-rel="/data/schedule/add">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <input type="hidden" name="id" value="">
                <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                <input type="hidden" name="period" value="60"/>
                <div class="modal-header">
                    <h5 class="modal-title">Задание на загрузку: <span class="supplier-title" id="title_"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Наименование:</span><input type="text" onkeyup="javascript:{$('#title_').text($(this).val());}" class="form-control" placeholder="Наименование" aria-describedby="basic-addon1" name="title" value=""></div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group"><span class="input-group-addon" id="basic-addon2">FTP:</span><input type="text" class="form-control http-link" placeholder="Ссылка" aria-describedby="basic-addon2" name="remote_srv" value=""></div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group"><span class="input-group-addon" id="basic-addon3">Логин:</span><input type="text" class="form-control" placeholder="логин" aria-describedby="basic-addon3" name="remote_user" value=""></div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group"><span class="input-group-addon" id="basic-addon4">Пароль:</span><input type="password" class="form-control inn" placeholder="Пароль" aria-describedby="basic-addon4" name="remote_pass" value=""></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Расписание:</h4>
                        </div>
                        <div class="col-md-12">
                            <div class="btn-group periods">
                                <button data-ref="60" onclick="javascript:$('input[name=period]').val(60);$('.periods button').removeClass('btn-primary');$(this).addClass('btn-primary');" type="button" class="btn btn-default btn-primary" aria-expanded="false">Каждый час</button>
                                <button data-ref="120" onclick="javascript:$('input[name=period]').val(120);$('.periods button').removeClass('btn-primary');$(this).addClass('btn-primary');" type="button" class="btn btn-default" aria-expanded="false">Каждые 2 часа</button>
                                <button data-ref="240" onclick="javascript:$('input[name=period]').val(240);$('.periods button').removeClass('btn-primary');$(this).addClass('btn-primary');" type="button" class="btn btn-default" aria-expanded="false">Каждые 4 часа</button>
                                <button data-ref="1440" onclick="javascript:$('input[name=period]').val(1440);$('.periods button').removeClass('btn-primary');$(this).addClass('btn-primary');" type="button" class="btn btn-default" aria-expanded="false">Раз в день</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="javascript:page.submit({form:'#add_schedule'})">Сохранить</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отменить</button>
                </div>
            </div>
        </div>
    </div><!--end .modal-->
</div>
@endsection
