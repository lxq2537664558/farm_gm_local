var href = window.location.href;
var href = href.split("&");
var uid = href[3].slice(4);
var mid = href[4].slice(4);
function checktable(issearch){
    $.ajax({
         url:ipaddress+"index.php?m=Agent&c=User&a=goldRecords",
         data : {
            "uid" : uid,
            "mid" : mid,
            "page_name" : "channelManagement",
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
                for(var i = 0; i<data.data.length; i++){
                    $('#mytable tbody').append(`<tr><td>${data.data[i].uid}</td><td>${data.data[i].username}</td><td>${data.data[i].money}</td><td>${data.data[i].type}</td><td>${data.data[i].time}</td></tr>`)
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
    checktable($('#search input').val());
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
        checktable($('#search input').val());
    });
    $('#prev').click(function(){
        page--;
        if(page==1){
            $('#prev').attr('disabled',true).css('background','gray');
        }
        $('#next').attr('disabled',false).css('background','#4B97EB');
        checktable($('#search input').val());
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
        checktable($('#search input').val());
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