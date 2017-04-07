<div class="panel-body">
    <div class="modal fade" id="catalogs" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <input type="hidden" name="id" value="">
                <div class="modal-header">
                    <h5 class="modal-title">Каталог товаров</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 js-container catalog-container" data-ref="/data/catalogs" data-func="catalogLoader"></div>
                        <div class="col-md-8 js-container goods-container" data-ref="/data/goods" data-func="goodsLoader"></div>
                    </div>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div><!--end .modal-->
</div>
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
    var checkLock=false,catalog = {
        selected:{},
        currentid:"",
        previd:null,
        container:$(".catalog-container"),
        level:0,
        settings:{
            cols:4
        },
        surf:function(id){
            catalog.previd = catalog.currentid;
            catalog.currentid = id;
            page.filters.data.parent_id=id;
            page.reload();
        },
        surfback:function(){
            page.filters.data.parent_id=catalog.previd;
            page.reload();
        },
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
        loader:function(d){
            var s = '<ul class="catalog-navigation">';
            for(var i in d){
                var p=d[i], dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,status_ico='<i class="fa fa-2x fa-circle-o-notch" aria-hidden="true"></i>';
                s+=catalog.drawcat(p);
                page.filters.data.f++;
            }
            s+= '</ul>';
            catalog.container.append(s);
        },
        goodsLoader:function(d){
            for(var i in d)catalog.drawgood(d[i]);
        },
        drawgood:function(p){
            var s = '<div class="catalog-good-item';
            s+= (typeof(catalog.selected[p.catalog_id])!="undefined" || (typeof(catalog.selected.goods)!="undefined" && typeof(catalog.selected.goods[p.id])!="undefined"))?" good-checked":"";
            s+= '" data-id="'+p.id+'" data-cat-id="'+p.catalog_id+'">';
            s+= '<img src="/img/'+p.image+'" alt="" style="height:8rem;"/>';
            s+= '<div class="catalog-good-title">'+p.title+'</div>';
            (typeof(catalog.selected[p.catalog_id])!="undefined" || (typeof(catalog.selected.goods)!="undefined" && typeof(catalog.selected.goods[p.id])!="undefined"))
                ?s+= '<a href="javascript:catalog.checkGood('+p.id+');" class="check-good good-checked" data-id="'+p.id+'"><i class="fa fa-check-square-o"></i></a>'
                :s+= '<a href="javascript:catalog.checkGood('+p.id+');" class="check-good" data-id="'+p.id+'"><i class="fa fa-square-o"></i></a>';
            s+= '</div>';
            //catalog.previd=(p.parent_id!=null)?p.parent_id:null;
            $(s).appendTo(".goods-container");
        },
        checkGood:function(i){
            var $t = $(".check-good[data-id="+i+"] > i.fa"),n=$(".catalog-good-item[data-id="+i+"] > .catalog-good-title"),p = $(".catalog-good-item[data-id="+i+"]")
                check = (arguments.length>1)?arguments[1]:($t.hasClass("good-checked")?false:true),
                auto = (arguments.length>2)?arguments[2]:false;
            if(typeof(catalog.selected.goods)=="undefined")catalog.selected.goods = new Object();
            if(check){
                $t.addClass("good-checked");
                $t.addClass("fa-check-square-o");
                $t.removeClass("fa-square-o");
                p.addClass("good-checked");
                if(!auto)catalog.selected.goods[i] = n.text();
            }else {
                $t.removeClass("good-checked");
                $t.removeClass("fa-check-square-o");
                $t.addClass("fa-square-o");
                p.removeClass("good-checked");
                if(typeof(catalog.selected.goods[i])!="undefined")delete catalog.selected.goods[i];
            }
            $("input[name=goods]").val(Object.keys(catalog.selected.goods));
            catalog.goodsfordownload();
        },
        goodsfordownload:function(){
            var catalogs = arguments.length?arguments[0]:Object.keys(catalog.selected)
                goods = (arguments.length>1)?arguments[1]:((catalog.selected.goods)?Object.keys(catalog.selected.goods):[]),
                $cont = $((arguments.length>2)?arguments[2]:".goods-name");
            console.debug(catalog.selected);
            $.getJSON('/data/goodsfordownload',{catalog_id:catalogs},function(d,s,x){
                var qty = d.quantity+goods.length;
                $cont.html('('+qty+")");
            });
        },
        drawcat:function(p){
            var s = '<li class="catalog-navigation-item" data-id="'+p.id+'">';//,c = (typeof p.goods != "undefined")?p.goods:0;
            s+= (typeof(catalog.selected[p.id])!="undefined")
                ?'<a href="javascript:catalog.check('+p.id+');" class="check-catalog catalog-checked" data-id="'+p.id+'"><i class="fa fa-check-square-o"></i></a>&nbsp;'
                :'<a href="javascript:catalog.check('+p.id+');" class="check-catalog" data-id="'+p.id+'"><i class="fa fa-square-o"></i></a>&nbsp;';
            s+= '<a href="javascript:catalog.expand('+p.id+')"><i class="catalog-title">'+p.title+'</i>&nbsp;<span class="catalog-goods-count"></span></a>';
            s+= '</li>';
            return s;
        },
        check:function(i){
            var $t = $(".check-catalog[data-id="+i+"] > i.fa"),n=$(".catalog-navigation-item[data-id="+i+"] > a > i.catalog-title"),
                check = (arguments.length>1)?arguments[1]:($t.hasClass("catalog-checked")?false:true),
                auto = (arguments.length>2)?arguments[2]:false;
            //console.debug(store.catalogs[i]);
            if(store.catalogs && store.catalogs[i] && store.catalogs[i].childs)for(var ch in store.catalogs[i].childs)catalog.check(ch,check,false);
            if(check){
                $t.addClass("catalog-checked");
                $t.addClass("fa-check-square-o");
                $t.removeClass("fa-square-o");
                n.addClass("catalog-checked");
                catalog.selected[i] = n.text();
            }else{
                $t.removeClass("catalog-checked");
                $t.removeClass("fa-check-square-o");
                $t.addClass("fa-square-o");
                n.removeClass("catalog-checked");
                delete catalog.selected[i];
            }
            $("[data-cat-id="+i+"]").each(function(){catalog.checkGood($(this).attr("data-id"),$t.hasClass("catalog-checked"),check);});

            var t = Object.keys(catalog.selected),v = Object.values(catalog.selected);
            t.splice(t.indexOf('goods'),1);
            v.splice(v.indexOf('[object Object]'),1);
            $("input[name=catalogs]").val(t);
            $(".catalogs-name").html('('+t.length+')');
            $(".catalogs-name-list").html('<ul><li>'+v.join('</li><li>')+'</li></ul>');
            (auto)?catalog.goodsfordownload():{};
        },
        expand:function(i){
            var $t = $("a[data-id="+i+"]");
            catalog.container=$("li[data-id="+i+"] > ul");
            if($t.find(".fa").hasClass('catalog-expanded')){
                catalog.container.hide();
                $t.find(".fa").removeClass('catalog-expanded');
                //page.filters.data.catalog_id.splice(page.filters.data.catalog_id.indexOf(i),1);
                return;
            }else {
                $t.find(".fa").addClass('catalog-expanded');
                page.filters.data.catalog_id.splice(0);
                var cc = catalog._recurseArray(store.catalogs[i],"childs","id");console.debug(cc);
                page.filters.data.catalog_id = catalog._recurseArray(store.catalogs[i],"childs","id");
                catalog.container.show();
            }
            $(".goods-container").html("");
            page.load('.goods-container');
        },
        catalogs:function(t){
            console.debug(t);
            var recursivecatalogs=function(d,ss){
                var s = $('<ul class="catalog-navigation" style="display:none;"></ul>').appendTo(ss);
                for(var i in d){
                    var p = d[i],ss = $(catalog.drawcat(p)).appendTo(s);
                    store.catalogs[i]=p;
                    if(p.childs && !p.childs.length){
                        recursivecatalogs(p.childs,ss);
                    }
                }
                return s;
            };
            store.catalogs = new Object();
            var f = recursivecatalogs(t,catalog.container);
            f.show();
            catalog.container.removeClass("js-container");
            // $('.catalogs-id').each(function(){
            //     var $t = $(this),v = $(this).text().split(/,\s*/g),t=[];
            //     for(var i in v) {
            //         var n = store.catalogs[v[i]];
            //         if(n) t.push(n.title);
            //     }
            //     $t.text(t.join(', '));
            // });
            page.noscroll = false;
            return;

        },
        _recurse:function(w,c,f){
            var r = 0;
            if(typeof(w)=="undefined")return r;
            r+=(typeof(w[f])!="undefined")?w[f]:0;
            if(typeof(w[c])!="undefined") for(var i in w[c])r+=catalog._recurse(w[c][i],c,f);
            return r;
        },
        _recurseArray:function(w,c,f){
            var r = [];
            if(typeof(w)=="undefined")return r;
            if(typeof(w[f])!="undefined")r.push(w[f]);
            if(typeof(w[c])!="undefined")for(var i in w[c])r=r.concat(catalog._recurseArray(w[c][i],c,f));
            return r;
        }
    };
    window.goodsLoader = catalog.goodsLoader;
    window.catalogLoader = catalog.catalogs;
    //window._contentLoader=catalog.catalogs;
    $(document).ready(function(){
        page.noscroll = true;
    });
</script>
