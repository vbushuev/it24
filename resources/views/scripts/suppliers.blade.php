<script>
    function _contentLoader(d){
        console.debug(d);
        for(var i = 0;i<d.length;++i){
            var p=d[i], dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,status_ico='<i class="fa fa-2x fa-circle-o-notch" aria-hidden="true"></i>';

            s= '<div class="row item" data-rel="'+p.id+'">';
            s+= '<div class="col-md-2"><b>'+p.title+'</b></div>';
            s+= '<div class="col-md-4"><div class="multirows">'+p.protocol+'<br/><i style="color:blue;">'+p.link.substr(0,32)+'...'+'</i></div></div>';
            s+= '<div class="col-md-2">'+p.inn+'</div>';
            s+= '<div class="col-md-2">'+p.last.replace(dateExp,"$3.$2.$1 $4:$5:$6")+'</div>';
            s+= '<div class="col-md-1"><div class="multirows">'+periodTranslate(p.period)+'</div></div>';
            s+= '<div class="col-md-1"><a href="javascript:$(\'#supplier_'+p.id+'\').modal();"><i class="fa fa fa-pencil edit-supplier" style="color:green;"></i></a></div>';
            //edit window
            s+= '<div class="modal fade" id="supplier_'+p.id+'" data-rel="/data/supplierupdate"><div class="modal-dialog modal-lg"><div class="modal-content">';
            s+= '<input type="hidden" name="id" value="'+p.id+'">';
            s+= '<input type="hidden" name="protocol_id" value="'+p.protocol_id+'">';
            s+= '<div class="modal-header"><h5 class="modal-title">Редактирование поставщика: <span class="supplier-title" id="title_'+p.id+'">'+p.title+'</span></h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            s+= '<div class="modal-body">';
            s+= '   <div class="row">';
            s+= '       <div class="col-md-12">';
            s+= '           <div class="input-group"><span class="input-group-addon" id="basic-addon1">Наименование:</span><input type="text" onkeyup="javascript:{$(\'#title_'+p.id+'\').text($(this).val());}" class="form-control" placeholder="Наименование" aria-describedby="basic-addon1" name="title" value="'+p.title+'"></div>';
            s+= '       </div>';
            s+= '       <div class="col-md-12">';
            s+= '           <div class="input-group"><span class="input-group-addon" id="basic-addon2">Ссылка:</span><input type="text" class="form-control http-link" placeholder="Ссылка" aria-describedby="basic-addon2" name="link" value="'+p.link+'"></div>';
            s+= '       </div>';
            s+= '       <div class="col-md-6">';
            s+= '           <div class="input-group"><span class="input-group-addon" id="basic-addon3">Код:</span><input type="text" class="form-control" placeholder="Код" aria-describedby="basic-addon3" name="code" value="'+p.code+'"></div>';
            s+= '       </div>';
            s+= '       <div class="col-md-6">';
            s+= '           <div class="input-group"><span class="input-group-addon" id="basic-addon3">ИНН:</span><input type="text" class="form-control inn" placeholder="Наименование" aria-describedby="basic-addon3" name="inn" value="'+p.inn+'"></div>';
            s+= '       </div>';
            s+= '   </div>';
            s+= '   <div class="row"><h4>Расписание:</h4>';
            s+= '       <div class="col-md-12">';
            s+= '       <div class="btn-group periods"><input type="hidden" name="period" val="'+p.period+'"/>';
            s+= '           <button onclick="javascript:$(\'input[name=period]\').val(60);$(\'.periods button\').removeClass(\'btn-primary\');$(this).addClass(\'btn-primary\');" type="button" class="btn btn-'+(p.period==60?'primary':'default')+'" aria-expanded="false">Каждый час</button>';
            s+= '           <button onclick="javascript:$(\'input[name=period]\').val(120);$(\'.periods button\').removeClass(\'btn-primary\');$(this).addClass(\'btn-primary\');" type="button" class="btn btn-default '+(p.period==120?'btn-primary':'')+'" aria-expanded="false">Каждые 2 часа</button>';
            s+= '           <button onclick="javascript:$(\'input[name=period]\').val(240);$(\'.periods button\').removeClass(\'btn-primary\');$(this).addClass(\'btn-primary\');" type="button" class="btn btn-default '+(p.period==240?'btn-primary':'')+'" aria-expanded="false">Каждые 4 часа</button>';
            s+= '           <button onclick="javascript:$(\'input[name=period]\').val(1440);$(\'.periods button\').removeClass(\'btn-primary\');$(this).addClass(\'btn-primary\');" type="button" class="btn btn-default '+(p.period==1440?'btn-primary':'')+'" aria-expanded="false">Раз в день</button>';
            s+= '       </div>';
            s+= '       </div>';
            s+= '   </div>';
            s+= '</div>';
            s+= '<div class="modal-footer">';
            s+= '   <button type="button" class="btn btn-primary" onclick="javascript:page.submit({form:\'#supplier_'+p.id+'\'})">Обновить</button>';
            s+= '   <button type="button" class="btn btn-default" data-dismiss="modal">Отменить</button>';
            s+= '</div>';
            s+= '</div></div></div><!--end .modal-->';
            s+= '</div><!--end .row-->';
            $("#js-container").append(s);
        }
    }
</script>
