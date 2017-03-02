<script>
function _contentLoader(d){
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
        page.filters.data.f++;
    }
}
</script>
