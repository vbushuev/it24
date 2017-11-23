@extends('layouts.app')
@section('content')
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4></h4>
        </div>
        <div class="panel-body">
            <div class="form-horizontal form" role="form" id="user" data-rel="/data/user/edit">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{$user->id}}" />
                <div class="form-group">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Имя:</span><input type="text"  class="form-control" placeholder="Имя" aria-describedby="basic-addon1" name="name" value="{{$user->name}}"></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="input-group"><span class="input-group-addon" id="basic-addon2">E-mail:</span><input type="text" class="form-control http-link" placeholder="Ссылка" aria-describedby="basic-addon2" name="email" value="{{$user->email}}"></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="input-group"><span class="input-group-addon" id="basic-addon4">Пароль:</span><input type="password" class="form-control" placeholder="пароль" aria-describedby="basic-addon4" name="password" value=""></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-primary pull-right" onclick="javascript:page.submit({form:'#user'})">
                            Сохранить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
