var page={
    filters:{
        data:{
            f:0,
            l:24,
            brand_id:"",
            supply_id:""
        },
        filter:{
            brands:function(){
                filters.data.f = 0;
                filters.data.brand_id = arguments.length?arguments[0]:"";
                page.load();
            }
        },
        clear:function(){
            var k = arguments.length?(Array.isArray(arguments[0])?arguments[0]:[arguments[0]]):[];
            if(k.length)for(var i in k)this.data[k[i]]="";
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
    form:{
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
}
page.load();
page.filters.get.brands();
page.filters.get.suppliers();
$(window).scroll(function () {
    if(($(window).height() + $(window).scrollTop()+300) >= $(document).height() && !lock){
        lock = true;
        page.load();
    }
});
