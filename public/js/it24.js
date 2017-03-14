var priceNumber = function(d){
    var r = (d==null)?0:d, a = r.toString().split(/\./),na=[],n="",f="";
    if(d>=1000){
        na = a[0].split("").reverse();var c = 3;
        console.debug(na);
        for(var i in na){
            n+=na[i];
            if(--c<=0){
                n+=" ";
                c=3;
            }
        }
        r = n.split("").reverse().join("")+"."+a[1]
    }
    r+='&#8381;';
    return r;
};
var periodTranslate=function(period){
    switch(period){
        case 1440:
        case "1440":period="Раз в день";break;
        case 240:
        case "240":period="Каждые 4 часа";break;
        case 120:
        case "120":period="Каждые 2 часа";break;
        case 60:
        case "60":period="Каждый час";break;
        case 30:
        case "30":period="Каждые полчаса";break;
    }
    return period;
}
var barcodeDraw = function(c){
    if(c=="0"||c==0)return "";
    var code39 = {}, ret = '',ib=c.split(""),bc="",w=1,h=48;
    code39["0"] = "bwbwwwbbbwbbbwbw";code39["1"] = "bbbwbwwwbwbwbbbw";code39["2"] = "bwbbbwwwbwbwbbbw";code39["3"] = "bbbwbbbwwwbwbwbw";
    code39["4"] = "bwbwwwbbbwbwbbbw";code39["5"] = "bbbwbwwwbbbwbwbw";code39["6"] = "bwbbbwwwbbbwbwbw";code39["7"] = "bwbwwwbwbbbwbbbw";
    code39["8"] = "bbbwbwwwbwbbbwbw";code39["9"] = "bwbbbwwwbwbbbwbw";code39["A"] = "bbbwbwbwwwbwbbbw";code39["B"] = "bwbbbwbwwwbwbbbw";
    code39["C"] = "bbbwbbbwbwwwbwbw";code39["D"] = "bwbwbbbwwwbwbbbw";code39["E"] = "bbbwbwbbbwwwbwbw";code39["F"] = "bwbbbwbbbwwwbwbw";
    code39["G"] = "bwbwbwwwbbbwbbbw";code39["H"] = "bbbwbwbwwwbbbwbw";code39["I"] = "bwbbbwbwwwbbbwbw";code39["J"] = "bwbwbbbwwwbbbwbw";
    code39["K"] = "bbbwbwbwbwwwbbbw";code39["L"] = "bwbbbwbwbwwwbbbw";code39["M"] = "bbbwbbbwbwbwwwbw";code39["N"] = "bwbwbbbwbwwwbbbw";
    code39["O"] = "bbbwbwbbbwbwwwbw";code39["P"] = "bwbbbwbbbwbwwwbw";code39["Q"] = "bwbwbwbbbwwwbbbw";code39["R"] = "bbbwbwbwbbbwwwbw";
    code39["S"] = "bwbbbwbwbbbwwwbw";code39["T"] = "bwbwbbbwbbbwwwbw";code39["U"] = "bbbwwwbwbwbwbbbw";code39["V"] = "bwwwbbbwbwbwbbbw";
    code39["W"] = "bbbwwwbbbwbwbwbw";code39["X"] = "bwwwbwbbbwbwbbbw";code39["Y"] = "bbbwwwbwbbbwbwbw";code39["Z"] = "bwwwbbbwbbbwbwbw";
    code39["-"] = "bwwwbwbwbbbwbbbw";code39["."] = "bbbwwwbwbwbbbwbw";code39[" "] = "bwwwbbbwbwbbbwbw";code39["*"] = "bwwwbwbbbwbbbwbw";
    code39["$"] = "bwwwbwwwbwwwbwbw";code39["/"] = "bwwwbwwwbwbwwwbw";code39["+"] = "bwwwbwbwwwbwwwbw";code39["%"] = "bwbwwwbwwwbwwwbw";
    for(var i in ib)bc+=code39[ib[i]];
    console.debug(c+" => "+bc);
    //draw
    bc_c = bc.split("");
    for(var i in bc_c){
        if(bc_c[i]=='b'){
            ret += '<line x1="'+i*w+'" y1="0" x2="'+i*w+'" y2="'+h+'" style="stroke:rgb(0,0,0);stroke-width:'+w+'" />';

        }
        if(i%16==0)ret += '<text x="'+(6*w+16*(i/16)*w)+'" y="'+(h+16)+'" fill="black">'+ib[i/16]+'</text>';
    }
    ret = '<svg height="'+(h+32)+'" width="'+w*ib.length*16+'">'+ret+'</svg>';
    return ret;
}
var page={
    noscroll:false,
    filters:{
        data:{
            f:0,
            l:24,
            s:"",
            brand_id:"",
            supply_id:"",
            catalog_id:[]
        },
        search:function($t){
            var val = $t.val();
            if(val.length>3){
                page.filters.data.f=0;
                page.filters.data.s=val;
                page.load();
            }
        },
        searchName:function($t){
            var val = $t.val();
            if(val.length>3){
                page.filters.data.f=0;
                page.filters.data.n=val;
                page.load();
            }
        },
        clear:function(){
            var k = arguments.length?(Array.isArray(arguments[0])?arguments[0]:[arguments[0]]):[];
            if(k.length)for(var i in k){
                if(k[i]=="catalog_id"){
                    $(".filter-check").removeAttr("checked");
                    this.data[k[i]]=[];
                }else this.data[k[i]]="";
            }
            else for(k in this.data)this.data[k]="";
            this.data.f=0;
            this.data.l=24;
            page.load();
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
            catalogs:function(){
                var t = arguments.length?arguments[0]:"",cc=!(typeof $(t).attr("checked")=="undefined");
                page.filters.data.catalog_id = (typeof page.filters.data.catalog_id == "undefined")?[]:page.filters.data.catalog_id;
                if(!Array.isArray(page.filters.data.catalog_id))page.filters.data.catalog_id=[];
                if(cc){
                    $(t).removeAttr("checked");
                    delete page.filters.data.catalog_id[$(t).parent().attr("data-id")];
                    page.filters.data.catalog_id.splice(page.filters.data.catalog_id.indexOf($(t).parent().attr("data-id")),1);
                }
                else {
                    $(t).attr("checked","checked");
                    page.filters.data.catalog_id.push($(t).parent().attr("data-id"));
                }
                $(t).parent().find('ul.dropdown-menu li').each(function(){
                    //console.debug($(t).attr("checked")+" :"+$(this).attr("data-id"));
                    if(cc){
                        //delete page.filters.data.catalog_id[$(this).attr("data-id")];
                        page.filters.data.catalog_id.splice(page.filters.data.catalog_id.indexOf($(this).attr("data-id")),1);
                        $(this).find(".filter-check").removeAttr("checked");
                    }else{
                        page.filters.data.catalog_id.push($(this).attr("data-id"));
                        $(this).find(".filter-check").attr("checked","checked");
                    }
                });
                console.debug(page.filters.data.catalog_id);
                page.filters.data.f=0;
                page.load();
            }
        },
        get:{
            all:function(){
                page.filters.get.roles();
                page.filters.get.brands();
                page.filters.get.suppliers();
                page.filters.get.catalogs();
                $("body").trigger('it24:filters-loaded');
            },
            roles:function(){
                var jscontent = $(".roles").next("ul.dropdown-menu"),
                    combo =$(".role").next("ul.dropdown-menu");
                if(jscontent.length==0 && combo.length==0)return;
                $.ajax({
                    url:"/data/roles",
                    type:"GET",
                    dataType:"json",
                    success:function(d){
                        for(var i=0;i<d.length;++i){
                            var p=d[i];
                            store.roles[p.code]=p.title;
                            jscontent.append('<li><a href="javascript:{page.filters.data.f=0;page.filters.data.role=\''+p.code+'\';page.load();}">'+(p.title.length?p.title:'noname')+'</a></li>');
                            combo.append('<li><a href="javascript:{$(\'input[name=role]\').val(\''+p.code+'\');$(\'input[name=roleName]\').val(\''+p.title+'\');}">'+(p.title.length?p.title:'noname')+'</a></li>');
                        }
                    }
                });
            },
            brands:function(t){
                var jscontent = $(".brands").next("ul.dropdown-menu");
                if(jscontent.length==0)return;
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
                if(jscontent.length==0)return;
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
            catalogs:function(t){
                var jscontent = $(".catalogs").next("ul.dropdown-menu"),recursivecatalogs=function(c){
                    var s ='';
                    for(var i in c){
                        var p=c[i];
                        store.catalogs[p.id] = p.title;
                        s+='<li data-id="'+p.id+'" class="catalog-id'+(Array.isArray(p.childs)?'':' dropdown-submenu')+'">';
                        s+='<input class="filter-check" type="checkbox" onchange="{page.filters.filter.catalogs(this);}"/>';
                        s+='<a href="javascript:0" data-id="'+p.id+'" id="'+p.id+'" class="submenu'+(Array.isArray(p.childs)?'':' dropdown-toggle" data-toggle="dropdown" aria-expanded="false')+'">'+p.title+'</a>';
                        if(!Array.isArray(p.childs)){
                            s+='<ul class="dropdown-menu">';
                            s+=recursivecatalogs(p.childs);
                            s+='</ul>';
                        }
                        s+='</li>';
                    }
                    return s;
                };
                if(jscontent.length==0)return;
                $.ajax({
                    url:"/data/catalogs",
                    type:"GET",
                    dataType:"json",
                    success:function(d){
                        console.debug(d);
                        for(var i in d){
                            var p=d[i],s='',h='<input class="filter-check" type="checkbox" onchange="{page.filters.filter.catalogs(this);}"/>';
                            store.catalogs[p.id] = p.title;
                            s+='<li data-id="'+p.id+'" '+(Array.isArray(p.childs)?'':'class="dropdown-submenu"')+'>';
                            s+=h;
                            s+='<a href="javascript:0" data-id="'+p.id+'" id="'+p.id+'" class="catalogs submenu'+(Array.isArray(p.childs)?'':' dropdown-toggle" data-toggle="dropdown" aria-expanded="false')+'">'+p.title+'</a>';
                            if(!Array.isArray(p.childs)){
                                s+='<ul class="dropdown-menu">';
                                s+=recursivecatalogs(p.childs);
                                s+='</ul>';
                            }
                            s+='</li>'
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
        console.debug("loading page with data from "+$("#js-container").attr("data-ref"));
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
var store={
    roles:{},
    catalogs:{}
}
$(document).ready(function(){
    page.filters.get.all();
    $(window).scroll(function () {
        if(page.noscroll)return;
        if(($(window).height() + $(window).scrollTop()+300) >= $(document).height() && !lock){
            lock = true;
            page.load();
        }
    });
    page.load();
});
