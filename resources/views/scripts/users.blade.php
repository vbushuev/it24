<script>
    var lock = false;
    var filters = {
        data:{
            lastId:0,
            brand_id:"",
            supply_id:""
        },
        filter:{
            brands:function(){
                filters.data.lastId = 0;
                filters.data.brand_id = arguments.length?arguments[0]:"";
                pageLoad();
            }
        },
        get:{
            brands:function(t){
                var jscontent = $(".brands").next("ul.dropdown-menu");
                $.ajax({
                    url:"/data/brands",
                    type:"GET",
                    dataType:"json",
                    success:function(d){
                        console.debug(d);
                        for(var i=0;i<d.length;++i){
                            var p=d[i];
                            jscontent.append('<li><a href="javascript:{filters.data.lastId=0;filters.data.brand_id='+p.id+';pageLoad();}">'+(p.title.length?p.title:'noname')+'</a></li>');
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
                            jscontent.append('<li><a href="javascript:{filters.data.lastId=0;filters.data.supply_id='+p.id+';pageLoad();}">'+p.title+'</a></li>');
                        }
                    }
                });
            }
        }
    };
    function pageLoad(){
        $.ajax({
            url:"/data/goods",
            type:"GET",
            data:filters.data,
            dataType:"json",
            beforeSend:function(){
                console.debug("lastId="+filters.data.lastId+" "+$("#js-container"));
                if(!filters.data.lastId)$("#js-container").html("");
            },
            success:function(d){
                for(var i = 0;i<d.length;++i){
                    var p=d[i], dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,status_ico='<i class="fa fa-2x fa-circle-o-notch" aria-hidden="true"></i>',
                        units = (p.unit==0)?'шт':p.unit,
                        id = 'S'+('0000000000'+p.id).substring(p.id.length),
                        img = '/img/'+p.image;

                    s = '<div class="row item">';
                    s+= '<div class="col-md-1"><img src="'+img+'" alt="" style="height:4rem;"/></div>';
                    s+= '<div class="col-md-2"><div class="multirows">'+id+'</div></div>';
                    //s+= '<div class="col-md-1"><div class="multirows">'+p.category+'</div></div>';
                    s+= '<div class="col-md-3"><div class="multirows"><b>'+p.brand+'</b><br /><br />'+p.title+'</div></div>';
                    s+= '<div class="col-md-2"><div class="multirows"><b>'+p.supplier+'</b><br/>Артикул:<b style="float:right;display:inline-block;">'+p.sku+'</b><br/>Код:<b style="float:right;">'+p.sid+'</b></div></div>';
                    s+= '<div class="col-md-1"><div class="multirows">'+p.price+'&#8381;</div></div>';
                    s+= '<div class="col-md-1"><div class="multirows">'+p.pack+' '+units+'</div></div>';
                    s+= '<div class="col-md-2"><div class="multirows">Вес: '+p.weight+'кг<br/>Размеры: '+p.width+'x'+p.height+'x'+p.depth+'см</div></div>';
                    s+= '</div>';
                    $("#js-container").append(s);
                    filters.data.lastId=p.id;
                }

            },
            complete:function(){console.debug("complete");lock=false;},
        });
    }
    pageLoad();
    filters.get.brands();
    filters.get.suppliers();
    $(window).scroll(function () {
        if(($(window).height() + $(window).scrollTop()+200) >= $(document).height() && !lock){
            lock = true;
            pageLoad();
        }
    });
</script>
