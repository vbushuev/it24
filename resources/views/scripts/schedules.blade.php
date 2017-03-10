<script>
    var addSchedule=function(){
        $('#add_schedule').modal();
        $('#add_schedule').attr("data-rel","/data/schedule/add");
        $('#add_schedule .modal-footer button.btn-primary').html('Сохранить');
    }
    var editSchedule=function(){
        $('#add_schedule').modal();
        $('#add_schedule').attr("data-rel","/data/schedule/edit");
        var edit = arguments.length?arguments[0]:false;
        if(edit==false)return;
        var p = JSON.parse($('#'+edit).text());
        $('#add_schedule .modal-title #title_').text(p.title);
        $('.periods button').removeClass('btn-primary');
        $('.periods button[data-ref='+p.period+']').addClass('btn-primary');
        for(var i in p)$('#add_schedule input[name='+i+']').val(p[i]);
        $('#add_schedule .modal-footer button.btn-primary').html('Обновить');
    }
    var download=function(){
        var edit = arguments.length?arguments[0]:false;
        if(edit==false)return;
        var p = JSON.parse($('#'+edit).text());
        console.debug("permanetly download task now");
    }
    function _contentLoader(d){
        console.debug(d);
        for(var i = 0;i<d.length;++i){
            var p=d[i], dateExp = /(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/,status_ico='<i class="fa fa-2x fa-circle-o-notch" aria-hidden="true"></i>',
                catalogs = (p.catalogs=='null'||p.catalogs==null)?'Все':p.catalogs;
            s= '<div class="row item" data-rel="'+p.id+'">';
            s+= '<div class="col-md-2"><b>'+p.title+'</b></div>';
            s+= '<div class="col-md-4"><div class="multirows"><i style="color:blue;">'+p.remote_srv.substr(0,32)+'...'+'</i></div></div>';
            s+= '<div class="col-md-4">'+catalogs+'</div>';
            s+= '<div class="col-md-1"><div class="multirows">'+periodTranslate(p.period)+'</div></div>';
            s+= '<div class="col-md-1">';
            s+= '<a href="javascript:editSchedule(\'raw_data_'+p.id+'\');"><i class="fa fa fa-2x fa-pencil edit-supplier" style="color:green;"></i></a>&nbsp;';
            s+= '<a href="javascript:download(\'raw_data_'+p.id+'\');"><i class="fa fa fa-2x fa-download edit-supplier" style="color:green;"></i></a>&nbsp;';
            s+= '</div>';
            s+= '<div id="raw_data_'+p.id+'" style="display:none">'+JSON.stringify(p)+'</div>';
            s+= '</div><!--end .row-->';
            $("#js-container").append(s);
        }
    }
</script>
