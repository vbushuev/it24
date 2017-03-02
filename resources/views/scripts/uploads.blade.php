<script>
    $.ajax({
        url:"/data/uploads",
        type:"GET",
        dataType:"json",
        success:function(d){
            console.debug(d);
            s = '<div class="row item">';
            for(var i = 0;i<d.length;++i){
                var p=d[i], dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/;
                s+= '<div class="col-md-1">'+p.id+'</div>';
                s+= '<div class="col-md-2">'+p.status+'</div>';
                s+= '<div class="col-md-2">'+p.title+'</div>';
                s+= '<div class="col-md-2">'+p.start.replace(/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,"$3.$2.$1")+'</div>';
                s+= '<div class="col-md-2">'+p.start.replace(dateExp,"$4:$5:$6")+'</div>';
                s+= '<div class="col-md-2">'+((p.end!=null)?p.end.replace(dateExp,"$4:$5:$6"):"")+'</div>';
                s+= '<div class="col-md-1">'+p.total+'</div>';
            }
            s+= '</div>';
            $("#js-container").append(s);
        }
    });
</script>
