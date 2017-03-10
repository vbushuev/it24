<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
            <a class="navbar-brand" href="#">IT<sup>24</sup></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                @can('uploads')
                <li @if(isset($panel)&&($panel=='uploads'))class="active"@endif><a href="/panel">Загрузки поставщиков @if(isset($panel)&&($panel=='uploads'))<span class="sr-only">(current)</span>@endif</a></li>
                @endcan
                <li @if(isset($panel)&&($panel=='downloads'))class="active"@endif><a href="/panel/downloads">Выгрузки клиентам @if(isset($panel)&&($panel=='downloads'))<span class="sr-only">(current)</span>@endif</a></li>
                @can('suppliers')
                <li @if(isset($panel)&&($panel=='suppliers'))class="active"@endif><a href="/panel/suppliers">Поставщики @if(isset($panel)&&($panel=='suppliers'))<span class="sr-only">(current)</span>@endif</a></li>
                @elsecan('schedules')
                <li @if(isset($panel)&&($panel=='schedules'))class="active"@endif><a href="/panel/schedules">Загрузки @if(isset($panel)&&($panel=='schedules'))<span class="sr-only">(current)</span>@endif</a></li>
                @endcan
                @can('users')
                <li @if(isset($panel)&&($panel=='users'))class="active"@endif><a href="/panel/users">Клиенты @if(isset($panel)&&($panel=='users'))<span class="sr-only">(current)</span>@endif</a></li>
                @endcan
                <!--<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Separated link</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">One more separated link</a></li>
                    </ul>
                </li>-->
            </ul>
            <!--<form class="navbar-form navbar-left">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search">
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
            </form>-->
            <ul class="nav navbar-nav navbar-right">
                <li @if(isset($panel)&&($panel=='goods'))class="active"@endif><a href="/panel/goods">Товары @if(isset($panel)&&($panel=='goods'))<span class="sr-only">(current)</span>@endif</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Настройки <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Профиль</a></li>
                        <li><a href="#">Сервер</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">О системе</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>
