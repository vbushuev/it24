<div class="modal fade" id="catalog" data-rel="/data/catalog/add">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <input type="hidden" name="id" value="">
            <div class="modal-header">
                <h5 class="modal-title">Наименование: <span class="supplier-title" id="title_"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Имя:</span><input type="text" onkeyup="javascript:{$('#title_').text($(this).val());}" class="form-control" placeholder="Наименование" aria-describedby="basic-addon1" name="title" value=""></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="javascript:page.submit({form:'#catalog'})">Сохранить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отменить</button>
            </div>
        </div>
    </div>
</div><!--end .modal-->
<script>
    var catalog = {
        container:$("#js-container"),
        level:0,
        add:function(){
            $('#catalog').modal();
            $('#catalog').attr("data-rel","/data/catalog/add");
            $('#catalog .modal-footer button.btn-primary').html('Сохранить');
        },
        edit:function(){
            $('#catalog').modal();
            $('#catalog').attr("data-rel","/data/catalog/edit");
            var edit = arguments.length?arguments[0]:false;
            if(edit==false)return;
            var p = JSON.parse($('#'+edit).text());
            for(var i in p)$('#catalog input[name='+i+']').val(p[i]);
            $('#catalog .modal-footer button.btn-primary').html('Обновить');
        },
        expand:function(id,l){
            var $t = $("a[data-id="+id+"]");
            catalog.level = l+1;
            catalog.container=$(".childs.child-"+id);
            if($t.find(".fa").hasClass('fa-minus')){
                catalog.container.hide();
                catalog.container.html("");
                $t.find(".fa").removeClass('fa-minus');
                $t.find(".fa").addClass('fa-plus');
                return;
            }
            $t.find(".fa").addClass('fa-minus');
            $t.find(".fa").removeClass('fa-plus');
            catalog.container.show();
            page.filters.data["parent_id"]=id;
            page.load();
        },
        loader:function(d){
            console.debug(d);
            for(var i = 0;i<d.length;++i){
                var p=d[i], dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,status_ico='<i class="fa fa-2x fa-circle-o-notch" aria-hidden="true"></i>';
                var role = (p.role=='admin')?'Администратор':'Клиент';
                s= '<div class="row item data-rel="'+p.id+'">';
                if(catalog.level>0)s+= '<div class="col-md-'+(catalog.level)+'"></div>';
                s+= '<div class="col-md-'+(11-catalog.level)+'">';
                s+= '<a href="javascript:catalog.expand('+p.id+','+p.level+',this);" data-id="'+p.id+'"><i class="fa fa fa fa-plus"></i></a>&nbsp;';
                s+= '<code>'+p.id+'</code>&nbsp;'+p.title
                s+= '</div>';
                s+= '<div class="col-md-1">';
                s+= '<a href="javascript:catalog.edit(\'raw_data_'+p.id+'\');"><i class="fa fa fa-2x fa-pencil"></i></a>&nbsp;';
                s+= '</div>';
                s+= '<div id="raw_data_'+p.id+'" style="display:none">'+JSON.stringify(p)+'</div>';
                s+= '</div>';
                s+= '<div class="childs child-'+p.id+'" style="display:none"></div>';
                catalog.container.append(s);
                page.filters.data.f++;
            }
        }
    };
    window._contentLoader=catalog.loader;
    $(document).ready(function(){
        page.noscroll = true;
    });
</script>
