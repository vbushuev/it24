<div class="panel-body">
    <div class="modal" id="catalogs" >
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
<div class="modal" id="catalog" data-rel="/data/catalog/add">
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
                        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Уровень:</span><input type="text" onkeyup="javascript:{$('#title_').text($(this).val());}" class="form-control" placeholder="Наименование" aria-describedby="basic-addon1" name="level" value="1"></div>
                        <div class="input-group"><span class="input-group-addon" id="basic-addon3">Родительский:</span><select class="form-control" placeholder="" aria-describedby="basic-addon3" name="parent_id"></select></div>
                    </div>
                    <div class="col-md-5 col-md-offset-1">
                        <div class="categories-list" data-url="/data/categories"></div>
                    </div>
                    <div class="col-md-5 col-md-offset-1 goods-container">
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
            $('#catalog .categories-list').html('');
        },
        drawcatSys2:function(p,internal){
            var s = '<li class="catalog-navigation-item" data-id="'+p.id+'">',suppl = store.suppliers[p.supply_id-1];//,c = (typeof p.goods != "undefined")?p.goods:0;
            s+= (p.internal_id == parseInt(internal.id))
                ?'<a data-id="'+p.id+'" class="external-checked" href="javascript:catalog.unlinkCategory2Catalog('+p.id+','+internal.id+');"><i class="fa fa-check-square-o"></i></a>'
                :'<a data-id="'+p.id+'" class="external" href="javascript:catalog.linkCategory2Catalog('+p.id+','+internal.id+');"><i class="fa fa-square-o"></i></a>';

            s+= '&nbsp;<a href="javascript:catalog.expand('+p.id+')" data-id="'+p.id+'">';
            s+= (!$.isArray(p.childs))?'<i class="fa fa-caret-right"></i>&nbsp;':"";
            s+='<span class="category-supply" data-id="'+p.id+'">'+(typeof(suppl)!="undefined"?suppl.title:"")+'</span>&nbsp;<i class="catalog-title">'+p.title+'</i>&nbsp;';

            s+='</a>';
            //s+= '<div id="catalog-raw-data-'+p.id+'" style="display:none;">'+JSON.stringify(p)+'</div>';
            s+= '</li>';
            return s;
        },
        linkCategory2Catalog:function(c,id){
            $.ajax({
                url:"/data/catalog/link",
                dataType:"json",
                data:{
                    internal_id:id,
                    id:c
                },
                success:function(d){
                    console.debug(d);catalog.edit(id);
                },
                error:function(s,x,e){
                    console.debug(s,x,e);
                }
            });
        },
        unlinkCategory2Catalog:function(c,id){
            $.ajax({
                url:"/data/catalog/unlink",
                dataType:"json",
                data:{
                    internal_id:id,
                    id:c
                },
                success:function(d){
                    console.debug(d);
                    catalog.edit(id);
                },
                error:function(s,x,e){
                    console.debug(s,x,e);
                }
            });
        },
        edit:function(){
            $('#catalog').modal();
            $('#catalog').attr("data-rel","/data/catalog/edit");
            console.debug(store.suppliers);
            var edit = arguments.length?arguments[0]:false,recursivecatalogs=function(d,ss,internal){
                var s = $('<ul class="catalog-navigation" style="display:none;"></ul>').appendTo(ss);
                for(var i in d){
                    var p = d[i],ss = $(catalog.drawcatSys2(p,internal)).appendTo(s);
                    store.catalogs[i]=p;
                    if(p.childs && !p.childs.length){
                        recursivecatalogs(p.childs,ss,internal);
                    }
                }
                return s;
            };;
            if(edit==false)return;
            var p = JSON.parse($('#catalog-raw-data-'+edit).text());
            for(var i in p)$('#catalog input[name='+i+'],#catalog select[name='+i+']').val(p[i]);
            $('#catalog .categories-list').each(function(){
                var $t = $(this),internal = p;

                page.dataLoad(this,function(d){
                    $t.html('');
                    var f = recursivecatalogs(d,$t,internal);
                    f.show();
                });
            });
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
            catalog._goods[p.id] = p;
            s+= (typeof(catalog.selected[p.catalog_id])!="undefined" || (typeof(catalog.selected.goods)!="undefined" && typeof(catalog.selected.goods[p.id])!="undefined"))?" good-checked":"";
            s+= '" data-id="'+p.id+'" data-cat-id="'+p.catalog_id+'" onclick="catalog.showgood('+p.id+')">';
            s+= '<img src="/img/'+p.image+'" alt="" style="height:8rem;"/>';
            s+= '<div class="catalog-good-title">'+p.title+'</div>';
            // (typeof(catalog.selected[p.catalog_id])!="undefined" || (typeof(catalog.selected.goods)!="undefined" && typeof(catalog.selected.goods[p.id])!="undefined"))
            //     ?s+= '<a href="javascript:catalog.checkGood('+p.id+');" class="check-good good-checked" data-id="'+p.id+'"><i class="fa fa-check-square-o"></i></a>'
            //     :s+= '<a href="javascript:catalog.checkGood('+p.id+');" class="check-good" data-id="'+p.id+'"><i class="fa fa-square-o"></i></a>';

            s+= '</div>';

            //catalog.previd=(p.parent_id!=null)?p.parent_id:null;
            $(s).appendTo(".goods-container");
        },
        showgood:function(gid){
            var s = '',p=catalog._goods[gid],dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,status_ico='<i class="fa fa-2x fa-circle-o-notch" aria-hidden="true"></i>',
                units = (p.unit==0)?'шт':p.unit,
                id = 'S'+('0000000000'+p.id).substring(p.id.length),
                img = '/img/'+p.image;;
            s+= '<div class="modal fade" id="good_'+p.id+'" data-rel="/data/good/adds"><div class="modal-dialog modal-lg"><div class="modal-content">';
            s+= '   <input type="hidden" name="id" value="'+p.id+'">';
            s+= '   <div class="modal-header"><h3 class="modal-title"><span class="supplier-title" id="title_'+p.id+'">'+p.title+'</span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></h3>';
            s+= '       <h5 class="category" data-id="'+p.category_id+'">'+p.category+'</h5>';
            s+= '   </div>';
            s+= '   <div class="modal-body">';
            s+= '       <div class="row">';
            s+= '           <div class="col-md-7">';
            s+= '               <img src="'+img+'" alt="'+p.title+'" width="420px"/>';
            s+= '           </div>';
            s+= '           <div class="col-md-5">';
            s+= '               <p>Идентификатор:<b style="float:right;display:inline-block;">'+id+'</b></p>';
            s+= '               <p>Бренд:<b style="float:right;display:inline-block;">'+p.brand+'</b></p>';
            @can('uploads')
            s+= '               <p>Поставщик:<b style="float:right;display:inline-block;">'+p.supplier+'</b></p>';
            @endcan
            s+= '               <p>Артикул:<b style="float:right;display:inline-block;">'+p.sku+'</b></p>';
            s+= '               <p>Штрихкод:<br />'+barcodeDraw(p.barcode)+'</p>';
            s+= '               <p>Кол-во в упакове:<b style="float:right;display:inline-block;">'+p.pack+'</b></p>';
            s+= '               <p>Цена за '+units+':<b style="float:right;display:inline-block;">'+priceNumber(p.price)+'</b></p>';
            s+= '               <input type="hidden" name="good_id" value="'+p.id+'" />';
            s+= '               <input type="hidden" name="user_id" value="{{$user->id}}" />';
            s+= '               <p><div class="input-group"><span class="input-group-addon" id="basic-addon1">Наценка:</span><input type="text" class="form-control" placeholder="Наценка" aria-describedby="basic-addon1" name="price_add" value="'+((typeof(p.price_add)!="undefined")?p.price_add:0)+'"></div></p>';
            s+= '               <br />';
            s+= '               <p>Вес:<b style="float:right;display:inline-block;">'+p.weight+'</b></p>';
            s+= '               <p>Ширина:<b style="float:right;display:inline-block;">'+p.width+'</b></p>';
            s+= '               <p>Высота:<b style="float:right;display:inline-block;">'+p.height+'</b></p>';
            s+= '               <p>Глубина:<b style="float:right;display:inline-block;">'+p.depth+'</b></p>';
            s+= '           </div>';
            s+= '       </div>';
            s+= '   </div>';
            s+= '   <div class="modal-footer">';
            s+= '       <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>';
            s+= '       <button type="button" class="btn btn-primary" onclick="javascript:page.submit({form:\'#good_'+p.id+'\'})">Сохранить</button>';
            s+= '   </div>';
            s+= '</div></div></div><!--end .modal-->';
            $('body').append(s);
            $("#good_"+p.id).modal();
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
            // s+= (typeof(catalog.selected[p.id])!="undefined")
            //     ?'<a href="javascript:catalog.check('+p.id+');" class="check-catalog catalog-checked"><i class="fa fa-check-square-o"></i></a>&nbsp;'
            //     :'<a href="javascript:catalog.check('+p.id+');" class="check-catalog"><i class="fa fa-square-o"></i></a>&nbsp;';
            s+= '<a href="javascript:catalog.expand('+p.id+')" data-id="'+p.id+'"><i class="fa fa-caret-right"></i>&nbsp;<i class="catalog-title">'+p.title+'</i>&nbsp;<sup>'+(typeof(p.goods)!="undefined"?'('+p.goods+')':'')+'</sup></a>';
            s+= '</li>';
            return s;
        },
        check:function(i){
            var $t = $(".check-catalog[data-id="+i+"] > i.fa"),n=$(".catalog-navigation-item[data-id="+i+"] > a > i.catalog-title"),
                check = (arguments.length>1)?arguments[1]:($t.hasClass("catalog-checked")?false:true),
                auto = (arguments.length>2)?arguments[2]:false;
            if(i=="all"){
                $t = $(".check-catalog-all >i.fa");
                for(var c in store.catalogs){
                    console.debug(store.catalogs[c]);
                    catalog.check(c,!$t.hasClass("catalog-checked"),false);
                }
                //return;
            }
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
                $t.find(".fa-caret-down").removeClass("fa-caret-down").addClass("fa-caret-right");
                //page.filters.data.catalog_id.splice(page.filters.data.catalog_id.indexOf(i),1);
                return;
            }else {
                $t.find(".fa").addClass('catalog-expanded');
                $t.find(".fa-caret-right").removeClass("fa-caret-right").addClass("fa-caret-down");
                page.filters.data.catalog_id.splice(0);
                var cc = catalog._recurseArray(store.catalogs[i],"childs","id");console.debug(cc);
                page.filters.data.catalog_id = catalog._recurseArray(store.catalogs[i],"childs","id");
                catalog.container.show();
            }
            $(".goods-container").html("");
            page.load('.goods-container');
        },
        catalogs:function(t){
            //console.debug(t);
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
            $('<ul class="catalog-navigation"><a href="javascript:catalog.check(\'all\');" class="check-catalog-all" data-id="all"><i class="fa fa-square-o"></i></a>&nbsp;Все</ul>').appendTo(catalog.container);
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
            //page.noscroll = false;
            return;

        },
        drawcatSys:function(p){
            var s = '<li class="catalog-navigation-item" data-id="'+p.id+'">';//,c = (typeof p.goods != "undefined")?p.goods:0;
            s+= '<a href="javascript:catalog.edit('+p.id+');" data-id="'+p.id+'"><i class="fa fa-gear"></i>&nbsp;';
            s+= (!$.isArray(p.childs))?'<i class="fa fa-caret-right"></i>&nbsp;':"";
            s+='</a>';
            s+= '<a href="javascript:catalog.expand('+p.id+')"><i class="catalog-title">'+p.title+'</i>&nbsp;<sup>'+(typeof(p.goods)!="undefined"?'('+p.goods+')':'')+'</sup></a>';
            s+= '<div id="catalog-raw-data-'+p.id+'" style="display:none;">'+JSON.stringify(p)+'</div>';
            s+= '</li>';
            return s;
        },

        listLoader:function(d){
            console.debug(d);
            var recursivecatalogs=function(d,ss){
                var s = $('<ul class="catalog-navigation" style="display:none;"></ul>').appendTo(ss);
                for(var i in d){
                    var p = d[i],ss = $(catalog.drawcatSys(p)).appendTo(s);
                    store.catalogs[i]=p;
                    if(p.childs && !p.childs.length){
                        recursivecatalogs(p.childs,ss);
                    }
                }
                return s;
            };
            store.catalogs = new Object();
            //$('<ul class="catalog-navigation"><a href="javascript:catalog.check(\'all\');" class="check-catalog-all" data-id="all"><i class="fa fa-square-o"></i></a>&nbsp;Все</ul>').appendTo(catalog.container);
            var f = recursivecatalogs(d,"#js-container");
            f.show();
            $("[name=parent_id]").html('').append('<option value="null">Главный</option>');
            for(var i in store.catalogs){
                var c = store.catalogs[i],lp = leftPad(c.level,"&nbsp;&nbsp;&nbsp;&nbsp;");
                //console.debug(c,lp);
                $("[name=parent_id]").append('<option value="'+c.id+'" data-level="'+c.level+'">'+lp+c.title+'</option>');
            }

            //catalog.container.removeClass("js-container");
            window.page.noscroll = true;
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
        },
        _goods:{}
    };
    window.goodsLoader = catalog.goodsLoader;
    window.catalogLoader = catalog.catalogs;
    window._contentLoader=catalog.listLoader;
    $(document).ready(function(){
        page.noscroll = true;
    });
</script>
