<script>
    function _contentLoader(d){
        console.debug(d);return;
        for(var i = 0;i<d.length;++i){
            var p=d[i], dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,status_ico='<i class="fa fa-2x fa-circle-o-notch" aria-hidden="true"></i>';
            switch(p.status){
                case "done":status_ico='<i class="fa fa-2x fa-check-circle" aria-hidden="true" style="color:green"></i>';break;
                case "failed":status_ico='<i class="fa fa-2x fa-minus-circle" aria-hidden="true" style="color:red"></i>';break;
                case "inprogress":status_ico='<i class="fa fa-spinner fa-pulse fa-2x fa-fw" aria-hidden="true"></i>';break;
            }
            s= '<div class="row item status-'+p.status+'" data-rel="'+p.id+'">';
            s+= '<div class="col-md-1">'+p.title+'</div>';
            s+= '<div class="col-md-2 '+p.status+'">'+status_ico+'<div class="error-message"><h5>'+p.error+'</h5>'+p.message+'</div></div>';
            s+= '<div class="col-md-2">'+p.title+'</div>';
            s+= '<div class="col-md-2">'+p.start.replace(/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,"$3.$2.$1")+'</div>';
            s+= '<div class="col-md-2">'+p.start.replace(dateExp,"$4:$5:$6")+'</div>';
            s+= '<div class="col-md-2">'+((p.end!=null)?p.end.replace(dateExp,"$4:$5:$6"):"")+'</div>';
            s+= '<div class="col-md-1 total">'+p.total+'</div>';
            s+= '</div>';
            $("#js-container").append(s);
        }
    }
    var all=true;
    setInterval(function(){
        console.debug("interval");
        var $items = all?$(".item"):$(".item.status-inprogress");
        $items.each(function(){
            var $t = $(this);
            $.ajax({
                url:"/data/uploads/progress",
                data:{tr_id:$t.attr('data-rel')},
                type:"GET",
                dataType:"json",
                success:function(d){
                    $t.find(".total").html(d.total);
                    all=false;
                }
            });
        });
    },10400);
</script>
