<script>
    function _contentLoader(d){
        for(var i = 0;i<d.length;++i){
            var p=d[i], dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,status_ico='<i class="fa fa-2x fa-circle-o-notch" aria-hidden="true"></i>';
            switch(p.status){
                case "done":status_ico='<i class="fa fa-2x fa-check-circle" aria-hidden="true" style="color:green"></i>';break;
                case "failed":status_ico='<i class="fa fa-2x fa-minus-circle" aria-hidden="true" style="color:red"></i>';break;
                case "inprogress":status_ico='<i class="fa fa-spinner fa-pulse fa-2x fa-fw" aria-hidden="true"></i>';break;
            }
            s= '<div class="row item status-'+p.status+'" data-rel="'+p.id+'">';
            s+= '<div class="col-md-1">'+p.id+'</div>';
            s+= '<div class="col-md-2 '+p.status+'">'+status_ico+'<div class="error-message"><h5>'+p.error+'</h5>'+p.message+'</div></div>';
            s+= '<div class="col-md-2">'+p.title+'</div>';
            s+= '<div class="col-md-2">'+p.start.replace(/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,"$3.$2.$1")+'</div>';
            s+= '<div class="col-md-2"><div class="multirows">';
            s+= 'Начало:<b style="float:right;display:inline-block;">'+p.start.replace(dateExp,"$4:$5:$6")+'</b><br/>'
            s+= 'Окончание:<b style="float:right;display:inline-block;">'+((p.end!=null&&typeof(p.end)!="undefined")?p.end.replace(dateExp,"$4:$5:$6"):'')+'</b>'
            s+= '</div></div>';
            s+= '<div class="col-md-2 summary">'+priceNumber(p.summary)+'</div>';
            s+= '<div class="col-md-1 total">'+p.total+'</div>';
            s+= '</div>';
            $("#js-container").append(s);
            page.filters.data.f++;
        }
        $("#js-container").trigger("content:ready");
    }
    var last=[];
    var getProgress = function (){
        var arg = arguments.length?arguments[0]:{};
        var $t = $(arg),tr_i = $t.attr('data-rel');
        $.ajax({
            url:"/data/uploads/progress",
            data:{tr_id:tr_i},
            type:"GET",
            dataType:"json",
            success:function(d){
                $t.find(".total").text(d.total);
                $t.find(".summary").html(priceNumber(d.sum));
                console.debug("was "+last[tr_i]+" now "+d.total);
                if($t.hasClass("status-failed"))return;
                if(typeof(last[tr_i])=="undefined" || last[tr_i]!=d.total || d.total == 0 )setTimeout(getProgress,1000,arg);
                last[tr_i] = d.total;
            }
        });
    }
    $("#js-container").on("content:ready",function(){
        //$(".item.status-inprogress,.item.status-failed").each(function(){
        $(".item.status-inprogress,.item.status-failed").each(function(){
            getProgress(this);
        });
        $("#js-container").unbind("content:ready");
    });

    /*
    setInterval(function(){
        var $items = $(".item.status-inprogress");
        $items.each(function(){
            var $t = $(this),
                tr_i = $t.attr('data-rel');
            $.ajax({
                url:"/data/uploads/progress",
                data:{tr_id:tr_i},
                type:"GET",
                dataType:"json",
                success:function(d){
                    $t.find(".total").text(d.total);
                    $t.find(".summary").html(priceNumber(d.sum));
                    last[tr_i] = d.total;
                }
            });
        });
    },2400);*/
</script>
