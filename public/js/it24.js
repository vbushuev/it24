var page={
    filters:{
        data:{
            f:0,
            l:24,
            s:"",
            brand_id:"",
            supply_id:"",
            category_id:[]
        },
        search:function($t){
            var val = $t.val();
            if(val.length>3){
                page.filters.data.f=0;
                page.filters.data.s=val;
                page.load();
            }
        },
        filter:{
            brands:function(){
                filters.data.f = 0;
                filters.data.brand_id = arguments.length?arguments[0]:"";
                page.load();
            },
            error:function(){
                page.filters.data.f=0;
                page.filters.data.error=(page.filters.data.error==1)?0:1;
                page.load();
            },
            categories:function(){
                var t = arguments.length?arguments[0]:"",cc=!(typeof $(t).attr("checked")=="undefined");
                page.filters.data.category_id = (typeof page.filters.data.category_id == "undefined")?[]:page.filters.data.category_id;
                if(cc){
                    $(t).removeAttr("checked");
                    delete page.filters.data.category_id[$(t).attr("data-id")];
                }
                else {
                    $(t).attr("checked","checked");
                    page.filters.data.category_id.push($(t).attr("data-id"));
                }
                $(t).parent().find('ul.dropdown-menu li').each(function(){
                    //console.debug($(t).attr("checked")+" :"+$(this).attr("data-id"));
                    if(cc){
                        delete page.filters.data.category_id[$(this).attr("data-id")];
                        $(this).find(".filter-check").removeAttr("checked");
                    }else{
                        page.filters.data.category_id.push($(this).attr("data-id"));
                        $(this).find(".filter-check").attr("checked","checked");
                    }
                });
                page.filters.data.f=0;
                page.load();
            }
        },
        clear:function(){
            var k = arguments.length?(Array.isArray(arguments[0])?arguments[0]:[arguments[0]]):[];
            if(k.length)for(var i in k){
                if(k[i]=="category_id"){
                    $(".filter-check").removeAttr("checked");
                    this.data[k[i]]=[];
                }else this.data[k[i]]="";
            }
            else for(k in this.data)this.data[k]="";
            this.data.f=0;
            this.data.l=24;
        },
        get:{
            brands:function(t){
                var jscontent = $(".brands").next("ul.dropdown-menu");
                $.ajax({
                    url:"/data/brands",
                    type:"GET",
                    dataType:"json",
                    success:function(d){
                        for(var i=0;i<d.length;++i){
                            var p=d[i];
                            jscontent.append('<li><a href="javascript:{page.filters.data.f=0;page.filters.data.brand_id='+p.id+';page.load();}">'+(p.title.length?p.title:'noname')+'</a></li>');
                        }
                    }
                });
            },
            suppliers:function(t){
                var jscontent = $(".suppliers").next("ul.dropdown-menu");
                $.ajax({
                    url:"/data/suppliers",
                    type:"GET",
                    dataType:"json",
                    success:function(d){
                        for(var i=0;i<d.length;++i){
                            var p=d[i];
                            jscontent.append('<li><a href="javascript:{page.filters.data.f=0;page.filters.data.supply_id='+p.id+';page.load();}">'+p.title+'</a></li>');
                        }
                    }
                });
            },
            categories:function(t){
                var jscontent = $(".categories").next("ul.dropdown-menu"),recursiveCategories=function(c){
                    var s ='';
                    for(var i in c){
                        var p=c[i];
                        s+='<li data-id="'+p.id+'" '+(Array.isArray(p.childs)?'':'class="dropdown-submenu"')+'>';
                        s+='<input class="filter-check" type="checkbox" onchange="{page.filters.filter.categories(this);}"/>';
                        s+='<a href="javascript:0" class="submenu'+(Array.isArray(p.childs)?'':' dropdown-toggle" data-toggle="dropdown" aria-expanded="false')+'">'+p.title+'</a>';
                        if(!Array.isArray(p.childs)){
                            s+='<ul class="dropdown-menu">';
                            s+=recursiveCategories(p.childs);
                            s+='</ul>';
                        }
                        s+='</li>';
                    }
                    return s;
                };
                $.ajax({
                    url:"/data/categories",
                    type:"GET",
                    dataType:"json",
                    success:function(d){
                        console.debug(d);
                        for(var i in d){
                            var p=d[i],s='',h='<input class="filter-check" type="checkbox" onchange="{page.filters.filter.categories(this);}"/>';
                            if(p.childs.length==0){
                                s+='<li><a href="javascript:{0}">'+p.title+'</a></li>';
                            }
                            else{
                                s+='<li class="dropdown-submenu">';
                                s+=h;
                                s+='<a href="javascript:{0}" class="submenu categories" data-toggle="dropdown">'+p.title+'</a>';
                                s+='<ul class="dropdown-menu">';
                                s+=recursiveCategories(p.childs);
                                s+='</ul></li>';
                            }
                            jscontent.append(s);
                        }
                    }
                });
            }
        }
    },
    refresh:function(){
        page.filters.clear();
        page.load();
    },
    reload:function(){
        page.filters.data.f-=page.filters.data.l;
        page.filters.data.f=(page.filters.data.f<page.filters.data.l)?0:page.filters.data.f;
        page.load();
    },
    load:function(){
        $.ajax({
            url:$("#js-container").attr("data-ref"),
            type:"GET",
            data:page.filters.data,
            dataType:"json",
            beforeSend:function(){
                if(!page.filters.data.f)$("#js-container").html("");
            },
            success:function(d){
                if(typeof _contentLoader!="undefined")_contentLoader(d);
            },
            complete:function(){lock=false;},
        });
    },
    submit:function(){
        if(!arguments.length)return;
        var p=arguments[0],args = {};
        $(p.form+' input').each(function(){
            var val = $(this).val();
            //todo add validate data
            //todo add check required
            args[$(this).attr("name")]=val;
        });
        console.debug(args);
        $.ajax({
            url:$(p.form).attr("data-rel"),
            dataType:"json",
            data:args,
            success:function(d){
                console.debug(d);
                $(p.form).modal('hide');
                document.location.reload();
                //page.reload();
            }
        });
    }
}
$(document).ready(function(){
    page.load();
    page.filters.get.brands();
    page.filters.get.suppliers();
    page.filters.get.categories();
    $(window).scroll(function () {
        if(($(window).height() + $(window).scrollTop()+300) >= $(document).height() && !lock){
            lock = true;
            page.load();
        }
    });
});
