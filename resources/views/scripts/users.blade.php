<div class="modal fade" id="user" data-rel="/data/user/add">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <input type="hidden" name="id" value="{{Auth::user()->id}}">
            <input type="hidden" name="role" value=""/>
            <div class="modal-header">
                <h5 class="modal-title">Пользователь: <span class="supplier-title" id="title_"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Имя:</span><input type="text" onkeyup="javascript:{$('#title_').text($(this).val());}" class="form-control" placeholder="Наименование" aria-describedby="basic-addon1" name="name" value=""></div>
                    </div>
                    <div class="col-md-10 col-md-offset-1">
                        <div class="input-group"><span class="input-group-addon" id="basic-addon2">E-mail:</span><input type="text" class="form-control http-link" placeholder="Ссылка" aria-describedby="basic-addon2" name="email" value=""></div>
                    </div>
                    <div class="col-md-10 col-md-offset-1">
                        <div class="input-group">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle role" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Роль <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Выберете</a></li>
                                    <li role="separator" class="divider"></li>
                                </ul>
                            </div><!-- /btn-group -->
                            <input class="form-control" type="text" readonly="readonly" name="roleName" placeholder="Выберете роль пользователя" value="">
                        </div><!-- /input-group -->
                    </div>
                    <div class="col-md-10 col-md-offset-1">
                        <div class="input-group"><span class="input-group-addon" id="basic-addon4">Пароль:</span><input type="password" class="form-control" placeholder="пароль" aria-describedby="basic-addon4" name="password" value=""></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="javascript:page.submit({form:'#user'})">Сохранить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отменить</button>
            </div>
        </div>
    </div>
</div><!--end .modal-->
<script>


    var user = {
        add:function(){
            $('#user').modal();
            $('#user').attr("data-rel","/data/user/add");
            $('#user .modal-footer button.btn-primary').html('Сохранить');
        },
        edit:function(){
            $('#user').modal();
            $('#user').attr("data-rel","/data/user/edit");
            var edit = arguments.length?arguments[0]:false;
            if(edit==false)return;
            var p = JSON.parse($('#'+edit).text()),
                roleName = store.roles[p.role];
            $('#user .modal-title #title_').text(p.name);
            $('input[name=roleName]').val(roleName);
            for(var i in p)$('#user input[name='+i+']').val(p[i]);
            $('#user .modal-footer button.btn-primary').html('Обновить');
        },
        loader:function(d){
            console.debug(d);
            for(var i = 0;i<d.length;++i){
                var p=d[i], dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,status_ico='<i class="fa fa-2x fa-circle-o-notch" aria-hidden="true"></i>';
                var role = (p.role=='admin')?'Администратор':'Клиент';
                s= '<div class="row item status-'+p.role+'" data-rel="'+p.id+'">';
                s+= '<div class="col-md-1">'+p.id+'</div>';
                s+= '<div class="col-md-3">'+p.name+'</div>';
                s+= '<div class="col-md-2"><a href="mailto:'+p.email+'">'+p.email+'</a></div>';
                s+= '<div class="col-md-2">'+p.created_at.replace(/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,"$3.$2.$1 $4:$5:$6")+'</div>';
                s+= '<div class="col-md-2"><div class="multirows">'+role+'</div></div>';
                s+= '<div class="col-md-2">';
                s+= '<a href="javascript:user.edit(\'raw_data_'+p.id+'\');"><i class="fa fa fa-2x fa-pencil"></i></a>&nbsp;';
                //s+= '<a href="javascript:download(\'raw_data_'+p.id+'\');"><i class="fa fa fa-2x fa-download" style="color:green;"></i></a>&nbsp;';
                s+= '</div>';
                s+= '<div id="raw_data_'+p.id+'" style="display:none">'+JSON.stringify(p)+'</div>';
                s+= '</div>';
                $("#js-container").append(s);
                page.filters.data.f++;
            }
        }
    };
    window._contentLoader=user.loader;
</script>
