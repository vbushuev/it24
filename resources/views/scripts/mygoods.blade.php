<div class="modal" id="user_catalog" data-rel="/user/data/catalog/add">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <input type="hidden" name="id" value="">
            <div class="modal-header">
                <h5 class="modal-title">Создать каталог: <span class="supplier-title" id="title_"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Имя:</span><input type="text" onkeyup="javascript:{$('#title_').text($(this).val());}" class="form-control" placeholder="Наименование" aria-describedby="basic-addon1" name="title" value=""></div>
                        <div class="input-group"><span class="input-group-addon" id="basic-addon3">Родительский:</span><select class="form-control" placeholder="" aria-describedby="basic-addon3" name="parent_id"><option value="0">Корневой</option></select></div>
                    </div>
                    <!-- <div class="col-md-5 col-md-offset-1 categories-list" data-url="/data/catalogs"></div>
                    <div class="col-md-5 col-md-offset-1 goods-container"></div> -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="javascript:page.submit({form:'#user_catalog'})">Сохранить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отменить</button>
            </div>
        </div>
    </div>
</div><!--end .modal-->
<script>
    window.UserCatalog = {
        _store:{},
        add:function(row){
            this._store[row.id]=row;
            this.fillCombo(row);
        },
        delete:function(row){
            $('[name=parent_id] option[value='+row.id+']').remove();
            delete this._store[row.id];

        },
        fillCombo:function(row){
            var getlevel=function(p){
                var r ='',glue = '&nbsp;&nbsp;';
                if(p.parent_id!="0"){
                    r+=glue;
                    r+=getlevel(UserCatalog._store[p.parent_id]);
                }
                return r;
            },l = getlevel(row);
            $('[name=parent_id]').append('<option value="'+row.id+'">'+l+row.title+'</option>');
        }
    };
    window.UserGood = {
        _store:{},
        add:function(row){this._store[row.id]=row;},
        check:function(i){
            return (this._store[i]!=undefined && this._store[i]!=null && typeof(this._store[i])!="undefined");
        },
        show:function(p){
            var s = '',dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,status_ico='<i class="fa fa-2x fa-circle-o-notch" aria-hidden="true"></i>',
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
        }
    };
    window.UserCatalogLoader = function(data,container){
        console.debug(data,container);
        var draw = function(p){
            var s = '<li class="user-catalog" data-id="'+p.id+'">';//,c = (typeof p.goods != "undefined")?p.goods:0;
            s+= '<a href="javascript:UserCatalogExpand('+p.id+')" data-id="'+p.id+'" class="">'
                +((p.childs.length)?'<i class="fa fa-caret-right"></i>':"&nbsp;")
                +'&nbsp;<i class="catalog-title">'+p.title+'</i>&nbsp;<sup>'+(typeof(p.goods)!="undefined"?'('+p.goods+')':'')+'</sup>';
            s+= '<a href=\'javascript:UserCatalogEdit('+JSON.stringify(p)+')\' class="user-catalog-edit"><i class="fa fa-gear"></i></a>';
            s+= '<a href=\'javascript:UserCatalogDelete('+JSON.stringify(p)+')\' class="user-catalog-trash"><i class="fa fa-trash"></i></a>';
            s+= '</a>'
            s+= '</li>';
            return s;
        },recursive=function(d,cont){
            var s = $('<ul class="catalog-navigation" style="display:none;"></ul>').appendTo(cont);
            for(var i in d){
                var p = d[i],ss = $(draw(p)).appendTo(s).droppable({
                    accept:".catalog-good-item",
                    drop:function(event,ui){
                        console.debug(event,ui);
                    }
                });
                window.UserCatalog.add(p);
                console.debug(p.childs)
                if(p.childs!=undefined && p.childs !=null && p.childs.length)recursive(p.childs,ss);
            }
            return s;
        };
        recursive(data,container).show();
    };
    window.UserCatalogEdit = function(d){
        $('#user_catalog .modal-title').html('<h5 class="modal-title">Редактирование: <span class="supplier-title" id="title_"></span></h5>');
        $('#user_catalog').attr('data-rel','/user/data/catalog/edit').modal();
        $('#user_catalog [name=id]').val(d.id);
        $('#user_catalog [name=title]').val(d.title);
        $('#user_catalog [name=parent_id]').val(d.parent_id);
        $('[name=parent_id] option').show();
        $('[name=parent_id] option[value='+d.id+']').hide();
    }
    window.UserCatalogDelete = function(d){
        var i = d.id,$t = $(".user-catalog[data-id="+d.id+"]");
        if(confirm('Подтвердите удаление каталога '+$t.text())){
            $.ajax({
                url:'/user/data/catalog/delete',
                data:{
                    id:d.id
                },
                success:function(){
                    $t.detach();
                    UserCatalog.delete(d);
                }
            });
        }
    }
    window.UserCatalogExpand = function (i){
        var $t = $(".user-catalog a[data-id="+i+"]"),container=$("li[data-id="+i+"] > ul");
        if(!$t.hasClass('selected')){
            $(".user-catalog a").removeClass('selected');
            $t.addClass('selected');
            // $('.catalog-copy-all').removeClass('disabaled');
        }
        if($t.find(".fa").hasClass('catalog-expanded')){
            container.hide();
            $t.find(".fa").removeClass('catalog-expanded');
            $t.find(".fa-caret-down").removeClass("fa-caret-down").addClass("fa-caret-right");
            //page.filters.data.catalog_id.splice(page.filters.data.catalog_id.indexOf(i),1);
            return;
        }else {
            container.show();
            $t.find(".fa").addClass('catalog-expanded');
            $t.find(".fa-caret-right").removeClass("fa-caret-right").addClass("fa-caret-down");
            page.filters.data.user_catalog_id = i;
            page.load('#userGoods');
        }
    };
    window.UserGoodLoader = function(data,container){
        console.debug(data);
        var draw = function(p,cc){
            window.UserGood.add(p);
            var s='<div class="catalog-good-item user-catalog-good" data-id="'+p.id+'" data-cat-id="'+p.catalog_id+'">';
            s+= '<img src="/img/'+p.image+'" alt="" style="height:8rem;"/>';
            s+= '<div class="catalog-good-title">'+p.title+'</div>';
            s+= '<a href="javascript:UserCatalogGoodUnlink('+p.id+','+p.catalog_id+');" class="user-good-link"><i class="fa fa-trash"></i></a>';
            s+= '<a href=\'javascript:window.UserGood.show('+JSON.stringify(p)+');\' class="user-good-show"><i class="fa fa-eye"></i></a>';
            s+= '</div>';
            $(s).appendTo(cc);//.draggable({ revert: true, helper: "clone" });
        },paginator = page.paginator("#"+container.attr("id"),data.from,data.limit,data.count,4);
        $('<div class="paginator-top"></div>').appendTo(container).append(paginator);
        var ccc=$('<div class="data-container"></div>').appendTo(container);
        for(var i in data.data)draw(data.data[i],ccc);
        $('<div class="paginator-footer"></div>').appendTo(container).append(paginator);
    };
    window.UserCatalogGoodLink = function(i){
        var uc = $(".user-catalog a.selected"),origin=$('.catalog-good-item[data-id='+i+']'),clone=origin.clone(),cont = $('#userGoods .data-container');
        if(uc.length==0){alert('Выберете каталог для добавления');return;}
        console.debug('animate: from '+origin.offset().top+':'+origin.offset().left+' to '+uc.offset().top+':'+uc.offset().left,'link good@'+i+' to usercatalog#'+uc.closest('.user-catalog').attr('data-id'));
        clone
            .css({
                opacity: '0.5',
                position: 'absolute',
                'z-index': '9999'
            })
            .offset({top: origin.offset().top-220,left: origin.offset().left-220})
            .appendTo(cont).animate({
                top: uc.offset().top,
                left: uc.offset().left,
                opacity:.2
            }, 400,"easeInOutExpo",function(){
                origin.hide();
                $(this).css({
                    position:'relative',
                    opacity:1,
                    top:0,
                    left:0
                }).find(".user-good-link").attr('href','javascript:UserCatalogGoodUnlink('+i+')').find(".fa").removeClass('fa-copy').addClass('fa-trash');
                $.ajax({
                    url:'/user/data/catalog/link',
                    dataType:'json',
                    data:{
                        user_catalog_id:uc.closest('.user-catalog').attr('data-id'),
                        good_id:i
                    },
                    success:function(d,x,s){console.debug(d);}
                });
            });

    }
    window.UserCatalogCopy=function(){
        var uc = $(".user-catalog a.selected"),c = $(".catalog-navigation-item a.selected");
        // if(uc.length==0 || c.length==0){alert('Выберете каталог для копирования');return;}
        if(c.length==0){alert('Выберете каталог для копирования');return;}
        $.ajax({
            url:'/user/data/catalog/copy',
            data:{
                user_catalog_id:(uc.length)?uc.attr('data-id'):'0',
                catalog_id:c.closest('.catalog-navigation-item').attr('data-id')
            },
            success:function(d,x,s){
                document.location.reload();
            }
        });
    };
    window.UserCatalogGoodUnlink = function(i,c){
        var $t = $('.user-catalog-good[data-id='+i+']');
        $.ajax({
            url:'/user/data/catalog/unlink',
            dataType:'json',
            data:{
                user_catalog_id:c,
                good_id:i
            },
            success:function(d,x,s){console.debug(d);$t.fadeOut();}
        });

    }
    window.GoodLoader = function(data,container){
        var draw = function(p,cc){
            if(window.UserGood.check(p.id))return;
            var s='<div class="catalog-good-item draggable" data-id="'+p.id+'" data-cat-id="'+p.catalog_id+'">';
            s+= '<img src="/img/'+p.image+'" alt="" style="height:8rem;"/>';
            s+= '<div class="catalog-good-title">'+p.title+'</div>';
            s+= '<a href="javascript:UserCatalogGoodLink('+p.id+');" class="user-good-link"><i class="fa fa-copy"></i></a>';
            s+= '<a href=\'javascript:window.UserGood.show('+JSON.stringify(p)+');\' class="user-good-show"><i class="fa fa-eye"></i></a>';
            // (typeof(catalog.selected[p.catalog_id])!="undefined" || (typeof(catalog.selected.goods)!="undefined" && typeof(catalog.selected.goods[p.id])!="undefined"))
            //     ?s+= '<a href="javascript:catalog.checkGood('+p.id+');" class="check-good good-checked" data-id="'+p.id+'"><i class="fa fa-check-square-o"></i></a>'
            //     :s+= '<a href="javascript:catalog.checkGood('+p.id+');" class="check-good" data-id="'+p.id+'"><i class="fa fa-square-o"></i></a>';
            s+= '</div>';
            $(s).appendTo(cc).draggable({ revert: true, helper: "clone" });
        },paginator = page.paginator("#"+container.attr("id"),data.from,data.limit,data.count,4);
        container.append(paginator);
        for(var i in data.data)draw(data.data[i],container);
        container.append(paginator);
    };
    window.CatalogLoader = function(data,container){
        var draw = function(p){
            var s = '<li class="catalog-navigation-item draggable" data-id="'+p.id+'">';//,c = (typeof p.goods != "undefined")?p.goods:0;
            s+= '<a href="javascript:CatalogExpand('+p.id+')">';
            s+= (p.childs && p.childs.length)?'<i class="fa fa-caret-right"></i>&nbsp;':"";
            s+= '<i class="catalog-title">'+p.title+'</i>&nbsp;<sup>'+(typeof(p.goods)!="undefined"?'('+p.goods+')':'')+'</sup></a>';
            s+= '<div id="catalog-raw-data-'+p.id+'" style="display:none;">'+JSON.stringify(p)+'</div>';
            s+= '</li>';
            return s;
        },recursivecatalogs=function(d,ss){
            var s = $('<ul class="catalog-navigation" style="display:none;"></ul>').appendTo(ss);
            for(var i in d){
                var p = d[i],ss = $(draw(p)).appendTo(s);
                if(p.childs && p.childs.length) recursivecatalogs(p.childs,ss);
            }
            return s;
        };
        recursivecatalogs(data,container).show();
        window.page.noscroll = true;
        return;
    }
    window.CatalogExpand=function(i){
        var $t = $(".catalog-navigation-item[data-id="+i+"] a:first"),container=$("li[data-id="+i+"] > ul");
        if(!$t.hasClass('selected')){
            $(".catalog-navigation-item a").removeClass('selected');
            $t.addClass('selected');
            $('.catalog-copy-all').removeClass('disabaled');
        }
        if($t.find(".fa").hasClass('catalog-expanded')){
            container.hide();
            $t.find(".fa").removeClass('catalog-expanded');
            $t.find(".fa-caret-down").removeClass("fa-caret-down").addClass("fa-caret-right");
            //page.filters.data.catalog_id.splice(page.filters.data.catalog_id.indexOf(i),1);
            return;
        }else {
            container.show();
            $t.find(".fa").addClass('catalog-expanded');
            $t.find(".fa-caret-right").removeClass("fa-caret-right").addClass("fa-caret-down");
            page.filters.data.catalog_id = [i];
            page.load('#pageGoods');
        }
        return;
    }
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
            var s = '<div class="catalog-good-item draggable';
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
            $(s).appendTo(".goods-container").draggable({ revert: true, helper: "clone" });
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
            page.load('#pageGoods');
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
            var s = '<li class="catalog-navigation-item draggable" data-id="'+p.id+'">';//,c = (typeof p.goods != "undefined")?p.goods:0;

            s+= '<a href="javascript:catalog.expand('+p.id+')">';
            s+= (!$.isArray(p.childs))?'<i class="fa fa-caret-right"></i>&nbsp;':"";
            s+= '<i class="catalog-title">'+p.title+'</i>&nbsp;<sup>'+(typeof(p.goods)!="undefined"?'('+p.goods+')':'')+'</sup></a>';
            s+= '<div id="catalog-raw-data-'+p.id+'" style="display:none;">'+JSON.stringify(p)+'</div>';
            s+= '</li>';
            return s;
        },

        listLoader:function(d,container){
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
            var f = recursivecatalogs(d,container);
            f.show();
            for(var i in store.catalogs)var c = store.catalogs[i];

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

    $(document).ready(function(){
        page.noscroll = true;
    });
</script>
