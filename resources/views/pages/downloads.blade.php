@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @include('navbar')
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>Загрузки от поставщиков</h4>
            <div class="row heading">
                <div class="col-md-1">#</div>
                <div class="col-md-2">Cтатус</div>
                <div class="col-md-2">Поставщик</div>
                <div class="col-md-2">Дата</div>
                <div class="col-md-2">Начало</div>
                <div class="col-md-2">Окончание</div>
                <div class="col-md-1">Кол-во записей</div>
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
