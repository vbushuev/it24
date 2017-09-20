<script>
function _contentLoader(d){
    console.debug(d);
    for(var i = 0;i<d.length;++i){

        var p=d[i], dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,status_ico='<i class="fa fa-2x fa-fw fa-spin fa-circle-o-notch" aria-hidden="true"></i>';
        switch(p.status){
            case "done":status_ico='<i class="fa fa-2x fa-check-circle" aria-hidden="true" style="color:green"></i>';break;
            case "failed":status_ico='<i class="fa fa-2x fa-minus-circle" aria-hidden="true" style="color:red"></i>';break;
            case "inprogress":status_ico='<i class="fa fa-spinner fa-pulse fa-2x fa-fw" aria-hidden="true"></i>';break;
        }
        s= '<div class="row item status-'+p.status+'" data-rel="'+p.id+'">';
        s+= '<div class="col-md-1">'+p.id+'</div>';
        s+= '<div class="col-md-2 '+p.status+'">'+status_ico+'<div class="error-message"><h5>'+p.error+'</h5>'+p.message+'</div></div>';
        s+= '<div class="col-md-2"><div class="multirows">'+p.title+'<br />Клиент: <b style="float:right">'+p.name+'</b></div></div>';
        s+= '<div class="col-md-2">'+p.timestamp.replace(/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,"$3.$2.$1")+'</div>';
        s+= '<div class="col-md-2"><div class="multirows">';
        s+= 'Начало:<b style="float:right;display:inline-block;">'+p.timestamp.replace(dateExp,"$4:$5:$6")+'</b><br/>'
        s+= 'Окончание:<b style="float:right;display:inline-block;">'+((p.time_end!=null&&typeof(p.time_end)!="undefined")?p.time_end.replace(dateExp,"$4:$5:$6"):'')+'</b>'
        s+= '</div></div>';
        s+= '<div class="col-md-2 summary">'+p.summary+'</div>';
        s+= '<div class="col-md-1 total">'+p.total+'</div>';
        s+= '</div>';
        $("#js-container").append(s);
        page.filters.data.f++;
    }
}
/*
var all=true;
setInterval(function(){
    var $items = all?$(".item"):$(".item.status-inprogress");
    $items.each(function(){
        var $t = $(this);
        $.ajax({
            url:"/data/download/progress",
            data:{tr_id:$t.attr('data-rel')},
            type:"GET",
            dataType:"json",
            success:function(d){
                $t.find(".total").text(d.total);
                $t.find(".summary").html(priceNumber(d.sum));
            }
        });
    });
    all=false;
},1400);*/
</script>
