@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @include('navbar')
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>Поставщики</h4>
            <div class="row heading">
                <div class="col-md-2">Наименование</div>
                <div class="col-md-4">Протокол/ссылка</div>
                <div class="col-md-2">ИНН</div>
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
