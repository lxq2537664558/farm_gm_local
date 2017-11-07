function checktable(ispagesize,issearch,isfield,isegt,iselt){
    $.ajax({
         url:ipaddress+"index.php?m=Home&c=Search&a=withdrawComplete",
         data : {
            "page_name" : "withdrawManagement",
            "pageSize" :  ispagesize,
            "search" : issearch,
            "table" : "withdrawals",
            "page" : page,
            "need_id_field" : "uid",
            "subtabulation_table": "user",
            "subtabulation_combine_field": "username",
            "field" : isfield,
            "egt" : isegt,
            "elt" : iselt
         },
         type: 'POST',
         dataType: 'json',
         async: false,
         success: function (data) {
            // console.log(data);
            // console.log(data.data);
            $('#mytable tbody').empty();
            $('#total_page').html(`当前<b id="current_page">${data.page.page}</b>/<b id="largest_page">${data.page.totalPage}</b>页`);
            if(data.data){
                for(var i = 0; i<data.data.length; i++){
                    // $('#mytable tbody').append(`<tr><td>${data.data[i].uid}</td><td>${data.data[i].id}</td><td>${data.data[i].money}</td><td>${data.data[i].collection_account}</td><td>${data.data[i].payee_name}</td><td>${data.data[i].bank}</td><td>${data.data[i].realname}</td><td>${data.data[i].yingfu}</td><td>${data.data[i].transfer_type}</td><td>${data.data[i].remarks}</td><td>${data.data[i].state}</td><td>${data.data[i].time}</td></tr>`)
                    $('#mytable tbody').append(`<tr><td>${data.data[i].uid}</td><td>${data.data[i].id}</td><td>${data.data[i].money}</td><td>${data.data[i].collection_account}</td><td>${data.data[i].payee_name}</td><td>${data.data[i].bank}</td><td>${data.data[i].realname}</td><td>${data.data[i].yingfu}</td><td>${data.data[i].state}</td><td>${data.data[i].time}</td></tr>`)
                }
                $('#search').show();
                $('#left_page').show();
                $('#right_page').show();
            }else{
                alert("没有此查询数据");
                $('#left_page').hide();
                $('#right_page').hide();
            }
        }
    })
}

var page = 1;
checktable(null);

$('.setting_display_num_btn').click(function(){
  if(isNaN($('.setting_display_num_input').val())){
      alert("不是一个数字");
  }else{
      checktable($('.setting_display_num_input').val(),$('#search .mohu').val(),$('#search select').val(),$('#search .more_money').val(),$('#search .less_money').val());
  }
})


// 下一页
if($('#largest_page').text()==1){
    $('#next').attr('disabled',true).css('background','gray');
}
$('#next').click(function(){
    page++;
    $('#prev').attr('disabled',false).css('background','#ED6033');
    if(page==$('#largest_page').text()){
        $(this).attr('disabled',true).css('background','gray');
    }
    checktable($('.setting_display_num_input').val());
})
// 上一页
$('#prev').attr('disabled',true).css('background','gray');
$('#prev').click(function(){
    page--;
    if(page==1){
        $('#prev').attr('disabled',true).css('background','gray');
    }
    $('#next').attr('disabled',false).css('background','#4B97EB');
    checktable($('.setting_display_num_input').val());
})

// 搜索
$('#search button').click(function(){
    page = 1;
    $('#prev').attr('disabled',true).css('background','gray');
    checktable($('.setting_display_num_input').val(),$('#search .mohu').val(),$('#search select').val(),$('#search .more_money').val(),$('#search .less_money').val());
    // 若最大页为1，设置下一页不可点
    if($('#largest_page').text()==1){
        $('#next').attr('disabled',true).css('background','gray');
    }else{
        $('#next').attr('disabled',false).css('background','#4B97EB');
    }
    $('#next').unbind("click");
    $('#prev').unbind("click");
    $('#jump').unbind("click");
    $('#next').click(function(){
        page++;
        $('#prev').attr('disabled',false).css('background','#ED6033');
        if(page==$('#largest_page').text()){
            $(this).attr('disabled',true).css('background','gray');
        }
        checktable($('#search .mohu').val(),$('#search select').val(),$('#search .more_money').val(),$('#search .less_money').val());
    });
    $('#prev').click(function(){
        page--;
        if(page==1){
            $('#prev').attr('disabled',true).css('background','gray');
        }
        $('#next').attr('disabled',false).css('background','#4B97EB');
        checktable($('.setting_display_num_input').val(),$('#search .mohu').val(),$('#search select').val(),$('#search .more_money').val(),$('#search .less_money').val());
    });
    $('#jump').click(function(){
    if(isNaN($('#jump_val').val())){
        alert("不是一个数字");
    }else{
        var jump_num = $('#jump_val').val(),
            jump_num = Number(jump_num);
        var largest_page = $('#largest_page').text(),
            largest_page = Number(largest_page);
        var current_page = $('#current_page').text(),
            current_page = Number(current_page);
        if(jump_num>largest_page||jump_num<=0){
            jump_num = current_page;
            $('#jump_val').val(current_page);
        }
        if(jump_num>1){
            $('#prev').attr('disabled',false).css('background','#ED6033');
        }
        if(jump_num==1){
            $('#prev').attr('disabled',true).css('background','gray');
        }
        if(largest_page==1){
            $('#next').attr('disabled',true).css('background','gray');
        }
        if(jump_num==largest_page){
            $('#next').attr('disabled',true).css('background','gray');
        }else{
            $('#next').attr('disabled',false).css('background','#4B97EB');
        }
        page = jump_num;
        checktable($('.setting_display_num_input').val(),$('#search .mohu').val(),$('#search select').val(),$('#search .more_money').val(),$('#search .less_money').val());
    }
})
})

// 跳转
$('#jump').click(function(){
    if(isNaN($('#jump_val').val())){
        alert("不是一个数字");
    }else{
        var jump_num = $('#jump_val').val(),
            jump_num = Number(jump_num);
        var largest_page = $('#largest_page').text(),
            largest_page = Number(largest_page);
        var current_page = $('#current_page').text(),
            current_page = Number(current_page);
        if(jump_num>largest_page||jump_num<=0){
            jump_num = current_page;
            $('#jump_val').val(current_page);
        }
        if(jump_num>1){
            $('#prev').attr('disabled',false).css('background','#ED6033');
        }
        if(jump_num==1){
            $('#prev').attr('disabled',true).css('background','gray');
        }
        if(largest_page==1){
            $('#next').attr('disabled',true).css('background','gray');
        }
        if(jump_num==largest_page){
            $('#next').attr('disabled',true).css('background','gray');
        }else{
            $('#next').attr('disabled',false).css('background','#4B97EB');
        }
        page = jump_num;
        checktable($('.setting_display_num_input').val());
    }
})
