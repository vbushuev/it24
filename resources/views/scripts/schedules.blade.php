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
                    <div class="col-md-10 col-md-offset-1">
                        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Наименование:</span><input type="text" onkeyup="javascript:{$('#title_').text($(this).val());}" class="form-control" placeholder="Наименование" aria-describedby="basic-addon1" name="title" value=""></div>
                    </div>
                    <div class="col-md-10 col-md-offset-1">
                        <div class="input-group"><span class="input-group-addon" id="basic-addon2">FTP:</span><input type="text" class="form-control http-link" placeholder="Ссылка" aria-describedby="basic-addon2" name="remote_srv" value=""></div>
                    </div>
                    <div class="col-md-5 col-md-offset-1">
                        <div class="input-group"><span class="input-group-addon" id="basic-addon3">Логин:</span><input type="text" class="form-control" placeholder="логин" aria-describedby="basic-addon3" name="remote_user" value=""></div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group"><span class="input-group-addon" id="basic-addon4">Пароль:</span><input type="password" class="form-control inn" placeholder="Пароль" aria-describedby="basic-addon4" name="remote_pass" value=""></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <h4>Выбор товаров:</h4>
                    </div>
                    <div class="col-md-10 col-md-offset-1">
                        <div class="input-group">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle catalogs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Категории товаров <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Выберете</a></li>
                                    <li role="separator" class="divider"></li>
                                </ul>
                            </div><!-- /btn-group -->
                            <input class="form-control" type="text" readonly="readonly" name="catalogsName" placeholder="Список категорий товаров" value="">
                            <input type="hidden" name="catalogs">
                        </div><!-- /input-group -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <h4>Расписание:</h4>
                    </div>
                    <div class="col-md-10 col-md-offset-1">
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
<script>
    var addSchedule=function(){
        $('#add_schedule').modal();
        $('#add_schedule').attr("data-rel","/data/schedule/add");
        $('#add_schedule .modal-footer button.btn-primary').html('Сохранить');
    }
    var editSchedule=function(){
        $('#add_schedule').modal();
        $('#add_schedule').attr("data-rel","/data/schedule/edit");
        var edit = arguments.length?arguments[0]:false;
        if(edit==false)return;
        var p = JSON.parse($('#'+edit).text());
        $('#add_schedule .modal-title #title_').text(p.title);
        $('.periods button').removeClass('btn-primary');
        $('.periods button[data-ref='+p.period+']').addClass('btn-primary');
        for(var i in p)$('#add_schedule input[name='+i+']').val(p[i]);
        if(typeof(p.catalogs)!="undefined" && typeof(store.catalogs)!="undefined"){
            var a = p.catalogs.split(/,\s/g),t=[];
            for(var i in a) t.push(store.catalogs[a[i]]);
            $("input[name=catalogsName]").val(t.join(", "));
        }
        $('#add_schedule .modal-footer button.btn-primary').html('Обновить');
    }
    var download=function(){
        var edit = arguments.length?arguments[0]:false;
        if(edit==false)return;
        var p = JSON.parse($('#'+edit).text());
        console.debug("permanetly download task now");
    }
    function _contentLoader(d){
        console.debug(d);
        for(var i = 0;i<d.length;++i){
            var p=d[i], dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,status_ico='<i class="fa fa-2x fa-circle-o-notch" aria-hidden="true"></i>',
                catalogs = (p.catalogs=='null'||p.catalogs==null)?'Все':p.catalogs;
            if(typeof(p.catalogs)!="undefined" && typeof(store.catalogs)!="undefined" && store.catalogs.length){
                var a = p.catalogs.split(/,\s/g),t=[];
                for(var i in a) t.push(store.catalogs[a[i]]);
                console.debug(a);
                console.debug(store.catalogs);
                console.debug(t);
                catalogs = t.join(", ");
            }
            s= '<div class="row item" data-rel="'+p.id+'">';
            s+= '<div class="col-md-2"><b>'+p.title+'</b></div>';
            s+= '<div class="col-md-4"><div class="multirows"><i style="color:blue;">'+p.remote_srv.substr(0,32)+'...'+'</i></div></div>';
            s+= '<div class="col-md-4">'+catalogs+'</div>';
            s+= '<div class="col-md-1"><div class="multirows">'+periodTranslate(p.period)+'</div></div>';
            s+= '<div class="col-md-1">';
            s+= '<a href="javascript:editSchedule(\'raw_data_'+p.id+'\');"><i class="fa fa fa-2x fa-pencil edit-supplier" style="color:green;"></i></a>&nbsp;';
            s+= '<a href="/download?id='+p.id+'"><i class="fa fa fa-2x fa-download edit-supplier" style="color:green;"></i></a>&nbsp;';
            s+= '</div>';
            s+= '<div id="raw_data_'+p.id+'" style="display:none">'+JSON.stringify(p)+'</div>';
            s+= '</div><!--end .row-->';
            $("#js-container").append(s);
        }
    }

    $(document).ready(function(){
        window.page.filters.filter.catalogs = function(t){
            var t = $(t).next(".submenu"),n=$("input[name=catalogsName]"),v=$("input[name=catalogs]");
            console.debug("reinjected function for ["+t.attr("data-id")+"]"+t.text());
            n.val(n.val()+((n.val().length)?", ":"")+t.text());
            if(typeof(t.attr("data-id"))!="undefined")v.val(v.val()+((v.val().length)?", ":"")+t.attr("data-id"));
        }
    });
</script>
