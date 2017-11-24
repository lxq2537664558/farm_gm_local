function checktable(ispagesize,issearch){
    $.ajax({
         url:ipaddress+"index.php?m=Home&c=User&a=gameData",
         data : {
            "page_name" : "channelManagement",
            "pageSize" :  ispagesize,
            "search" : issearch,
            "table" : "user",
            "page" : page
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
                console.log(data.data);
                for(var i = 0; i<data.data.length; i++){
                    $('#mytable tbody').append(`
                        <tr>
                            <td>${data.data[i].uid}</td>
                            <td>${data.data[i].username}</td>
                            <td>${data.data[i].diamond}</td>
                            <td>${data.data[i].treasure}</td>
                            <td>${data.data[i].recharge}</td>
                            <td>${data.data[i].withDraw}</td>

                            <td>${data.data[i].diff}</td>

                            <td>${data.data[i].cost}</td>
                            <td>${data.data[i].difference}</td>
                            <td>${data.data[i].depotLevel}</td>
                            <td>${data.data[i].stealTotalValue}</td>
                            <td>${data.data[i].beStolenTotalValue}</td>
                            <td>${data.data[i].steal_difference}</td>
                            <td>${data.data[i].dogNum}</td>
                            <td>${data.data[i].dogFoodNum}</td>
                            <td>${data.data[i].speedUpItemNum}</td>
                            <td>${data.data[i].consecrateNum}</td>
                            <td>[${data.data[i].farms}]</td>
                            <td>[${data.data[i].fishs}]</td>
                            <td>[${data.data[i].forests}]</td>
                            <td>[${data.data[i].mines}]</td>
                        </tr>
                    `)
                }
                // 分页以及搜索
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
      checktable($('.setting_display_num_input').val(),$('#search input').val());
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
    checktable(null);
})
// 上一页
$('#prev').attr('disabled',true).css('background','gray');
$('#prev').click(function(){
    page--;
    if(page==1){
        $('#prev').attr('disabled',true).css('background','gray');
    }
    $('#next').attr('disabled',false).css('background','#4B97EB');
    checktable(null);
})

// 搜索
$('#search button').click(function(){
    page = 1;
    $('#prev').attr('disabled',true).css('background','gray');
    checktable($('.setting_display_num_input').val(),$('#search input').val());
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
        checktable($('.setting_display_num_input').val(),$('#search input').val());
    });
    $('#prev').click(function(){
        page--;
        if(page==1){
            $('#prev').attr('disabled',true).css('background','gray');
        }
        $('#next').attr('disabled',false).css('background','#4B97EB');
        checktable($('.setting_display_num_input').val(),$('#search input').val());
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
        checktable($('.setting_display_num_input').val(),$('#search input').val());
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
        checktable(null);
    }
})
