@extends('layouts.app')
@section('content')
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4></h4>
        </div>
        <div class="panel-body">
            @if(isset($message))
            <div class="row"><div class="col-md-8 col-md-offset-2">
                <div class="message @if(isset($status) && $status =="failed") message-alert @endif">
                    {!!$message!!}
                </div>
            </div></div>
            @endif
            <form method="POST">
                <div class="form-horizontal form" role="form" id="support" data-rel="/support">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{$user->id}}" />
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-2">
                            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Тема:</span><input type="text"  class="form-control" placeholder="Тема" aria-describedby="basic-addon1" name="title" ></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-2">
                            <div class="input-group"><span class="input-group-addon" id="basic-addon2">Сообщение:</span><textarea class="form-control http-link" placeholder="Сообщение" aria-describedby="basic-addon2" name="message"></textarea></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-2">
                            <button type="submit" class="btn btn-primary pull-right">
                                Отправить
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
