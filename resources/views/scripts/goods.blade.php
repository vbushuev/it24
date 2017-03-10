<script>
function _contentLoader(d){
    for(var i = 0;i<d.length;++i){
        var p=d[i], dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,status_ico='<i class="fa fa-2x fa-circle-o-notch" aria-hidden="true"></i>',
            units = (p.unit==0)?'шт':p.unit,
            id = 'S'+('0000000000'+p.id).substring(p.id.length),
            img = '/img/'+p.image;

        s = '<div class="row item" data-id="'+p.id+'">';
        s+= '   <div class="col-md-1"><a href="javascript:{$(\'#good_'+p.id+'\').modal();}"><img src="'+img+'" alt="" style="height:4rem;"/></a></div>';
        s+= '   <div class="col-md-2"><div class="multirows">'+id+'</div></div>';
        //s+= '<div class="col-md-1"><div class="multirows">'+p.category+'</div></div>';
        s+= '   <div class="col-md-3"><div class="multirows"><b>'+p.brand+'</b><br /><br />'+p.title+'</div></div>';
        s+= '   <div class="col-md-2"><div class="multirows"><b>'+p.supplier+'</b><br/>Артикул:<b style="float:right;display:inline-block;">'+p.sku+'</b><br/>Код:<b style="float:right;">'+p.sid+'</b></div></div>';
        s+= '   <div class="col-md-1"><div class="multirows">'+priceNumber(p.price)+'</div></div>';
        s+= '   <div class="col-md-1"><div class="multirows">'+p.pack+' '+units+'</div></div>';
        s+= '   <div class="col-md-2"><div class="multirows">Вес: '+p.weight+'кг<br/>Размеры: '+p.width+'x'+p.height+'x'+p.depth+'см</div></div>';
        //editor
        s+= '   <div class="modal fade" id="good_'+p.id+'" data-rel="/data/goodupdate"><div class="modal-dialog modal-lg"><div class="modal-content">';
        s+= '   <input type="hidden" name="id" value="'+p.id+'">';
        s+= '   <div class="modal-header"><h3 class="modal-title"><span class="supplier-title" id="title_'+p.id+'">'+p.title+'</span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></h3>';
        s+= '       <h5 class="category" data-id="'+p.category_id+'">'+p.category+'</h5>';
        s+= '   </div>';
        s+= '   <div class="modal-body">';
        s+= '       <div class="row">';
        s+= '           <div class="col-md-8">';
        s+= '               <img src="'+img+'" alt="'+p.title+'" width="420px"/>';
        s+= '           </div>';
        s+= '           <div class="col-md-4">';
        s+= '               <p>Идентификатор:<b style="float:right;display:inline-block;">'+id+'</b></p>';
        s+= '               <p>Бренд:<b style="float:right;display:inline-block;">'+p.brand+'</b></p>';
        s+= '               <p>Поставщик:<b style="float:right;display:inline-block;">'+p.supplier+'</b></p>';
        s+= '               <p>Артикул:<b style="float:right;display:inline-block;">'+p.sku+'</b></p>';
        s+= '               <p>Штрихкод:<br />'+barcodeDraw(p.barcode)+'</p>';
        //s+= '               <p>Штрихкод:<b style="float:right;display:inline-block;">'+barcodeDraw("46173881")+'</b></p>';
        s+= '               <p>Кол-во в упакове:<b style="float:right;display:inline-block;">'+p.pack+'</b></p>';
        s+= '               <p>Цена за '+p.unit+':<b style="float:right;display:inline-block;">'+priceNumber(p.price)+'</b></p>';
        s+= '               <br />';
        s+= '               <p>Вес:<b style="float:right;display:inline-block;">'+p.weight+'</b></p>';
        s+= '               <p>Ширина:<b style="float:right;display:inline-block;">'+p.width+'</b></p>';
        s+= '               <p>Высота:<b style="float:right;display:inline-block;">'+p.height+'</b></p>';
        s+= '               <p>Глубина:<b style="float:right;display:inline-block;">'+p.depth+'</b></p>';
        s+= '           </div>';
        s+= '       </div>';
        s+= '   </div>';
        s+= '   <div class="modal-footer">';
        //s+= '       <button type="button" class="btn btn-primary" onclick="javascript:page.submit({form:\'#good_'+p.id+'\'})">Обновить</button>';
        s+= '       <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>';
        s+= '   </div>';
        s+= '</div></div></div><!--end .modal-->';
        s+= '</div>';
        $("#js-container").append(s);
        page.filters.data.f++;
    }
}
</script>
