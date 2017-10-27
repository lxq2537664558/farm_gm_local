var href = window.location.href;
var href = href.split("&");
var uid = href[3].slice(4);
var mid = href[4].slice(4);

function checktable(isstart_year,isstart_month,isstart_day,isend_year,isend_month,isend_day,isweek){
    // alert(start_year+'----'+start_month);
    $.ajax({
         url:ipaddress+"index.php?m=Home&c=User&a=promotionList",
         data : {
            "uid" : uid,
            "mid" : mid,
            "page_name" : "channelManagement",
            // "search" : issearch,
            "table" : "user",
            // "page" : page,
            "start_year": isstart_year,
            "start_month": isstart_month,
            "start_day": isstart_day,
            "end_year": isend_year,
            "end_month": isend_month,
            "end_day": isend_day,
            "week": isweek
         },
         type: 'POST',
         dataType: 'json',
         async: false,
         success: function (data) {
            // console.log(data);
            // console.log(data.data);
            $('#mytable tbody').empty();
            // $('#total_page').html(`当前<b id="current_page">${data.page.page}</b>/<b id="largest_page">${data.page.totalPage}</b>页`);
            $('.level').text(data.level);
            if(data.data){
                for(var i = 0; i<data.data.length; i++){
                // <td>${data.data[i].commission1}</td><td>${data.data[i].commission2}</td>
                    $('#mytable tbody').append(`<tr><td>${data.data[i].uid}</td><td>${data.data[i].username}</td><td>${data.data[i].register_time}</td><td>${data.data[i].recharge}</td><td>${data.data[i].cost}</td><td>${data.data[i].cost_commission}</td><td>${data.data[i].serviceCharge}</td><td>${data.data[i].service_commission}</td><td>${data.data[i].commission}</td></tr>`)
                }
                // 分页以及搜索
                $('.week_div').show();
                if($('.select_week').val()!=""){
                    $('#search_data').hide();
                }else{
                    $('#search_data').show();
                }
                // $('#left_page').show();
                // $('#right_page').show();
            }else{
                alert("没有此查询数据");
                // $('#left_page').hide();
                // $('#right_page').hide();
            }
        }
    })
}


// 选择周
$('.select_week').change(function(){
    $("#search_data select").val("");
    if($(this).val()!=""){
        $('#search_data').hide();
        $('.save_area').show();
        $('.save_current_week').val($(this).val());
        $('.save_current_fatherid').val(uid);
    }else{
        $('#search_data').show();
        $('.save_area').hide();
    }
})


//  处理选择日期
$('#start_year').change(function(){
    $('#start_month').val("");
    if($(this).val()!=""){
        $('#start_month').attr("disabled", false);
        if($(this).val()=="2017"){
            for(var i=1; i<10; i++){
                $('.startmonth'+i).attr("disabled",true);
            }
        }else{
            for(var i=1; i<10; i++){
                $('.startmonth'+i).attr("disabled",false);
            }
        }
    }else{
        $('#start_month').attr("disabled", true);
    }
})


$('#start_month').change(function(){
    $('#start_day').val("");
    if($(this).val()!=""){
        $('#start_day').attr("disabled", false);
        if($(this).val()=="-10"){
            for(var i=0; i<16; i++){
                $('.startday'+i).attr("disabled",true);
            }
        }else{
            for(var i=0; i<16; i++){
                $('.startday'+i).attr("disabled",false);
            }
        }
    }else{
        $('#start_day').attr("disabled", true);
    }
})



$('#end_year').change(function(){
    $('#end_month').val("");
    if($(this).val()!=""){
        $('#end_month').attr("disabled", false);
        if($(this).val()=="2017"){
            for(var i=1; i<10; i++){
                $('.endmonth'+i).attr("disabled",true);
            }
        }else{
            for(var i=1; i<10; i++){
                $('.endmonth'+i).attr("disabled",false);
            }
        }
    }else{
        $('#end_month').attr("disabled", true);
    }
})


$('#end_month').change(function(){
    $('#end_day').val("");
    if($(this).val()!=""){
        $('#end_day').attr("disabled", false);
        if($(this).val()=="-10"){
            for(var i=0; i<16; i++){
                $('.endday'+i).attr("disabled",true);
            }
        }else{
            for(var i=0; i<16; i++){
                $('.endday'+i).attr("disabled",false);
            }
        }
    }else{
        $('#end_day').attr("disabled", true);
    }
})


// var page = 1;
checktable(null);
// // 下一页
// if($('#largest_page').text()==1){
//     $('#next').attr('disabled',true).css('background','gray');
// }
// $('#next').click(function(){
//     page++;
//     $('#prev').attr('disabled',false).css('background','#ED6033');
//     if(page==$('#largest_page').text()){
//         $(this).attr('disabled',true).css('background','gray');
//     }
//     checktable(null);
// })
// // 上一页
// $('#prev').attr('disabled',true).css('background','gray');
// $('#prev').click(function(){
//     page--;
//     if(page==1){
//         $('#prev').attr('disabled',true).css('background','gray');
//     }
//     $('#next').attr('disabled',false).css('background','#4B97EB');
//     checktable(null);
// })


// 搜索
$('#search_btn').click(function(){
    // page = 1;
    // $('#prev').attr('disabled',true).css('background','gray');
    checktable($('#start_year').val(),$('#start_month').val(),$('#start_day').val(),$('#end_year').val(),$('#end_month').val(),$('#end_day').val(),$('.select_week').val());
    // 若最大页为1，设置下一页不可点
//     if($('#largest_page').text()==1){
//         $('#next').attr('disabled',true).css('background','gray');
//     }else{
//         $('#next').attr('disabled',false).css('background','#4B97EB');
//     }
//     $('#next').unbind("click");
//     $('#prev').unbind("click");
//     $('#jump').unbind("click");
//     $('#next').click(function(){
//         page++;
//         $('#prev').attr('disabled',false).css('background','#ED6033');
//         if(page==$('#largest_page').text()){
//             $(this).attr('disabled',true).css('background','gray');
//         }
//         checktable($('#start_year').val(),$('#start_month').val(),$('#start_day').val(),$('#end_year').val(),$('#end_month').val(),$('#end_day').val(),$('.select_week').val());
//     });
//     $('#prev').click(function(){
//         page--;
//         if(page==1){
//             $('#prev').attr('disabled',true).css('background','gray');
//         }
//         $('#next').attr('disabled',false).css('background','#4B97EB');
//         checktable($('#start_year').val(),$('#start_month').val(),$('#start_day').val(),$('#end_year').val(),$('#end_month').val(),$('#end_day').val(),$('.select_week').val());
//     });
//     $('#jump').click(function(){
//     if(isNaN($('#jump_val').val())){
//         alert("不是一个数字");
//     }else{
//         var jump_num = $('#jump_val').val(),
//             jump_num = Number(jump_num);
//         var largest_page = $('#largest_page').text(),
//             largest_page = Number(largest_page);
//         var current_page = $('#current_page').text(),
//             current_page = Number(current_page);
//         if(jump_num>largest_page||jump_num<=0){
//             jump_num = current_page;
//             $('#jump_val').val(current_page);
//         }
//         if(jump_num>1){
//             $('#prev').attr('disabled',false).css('background','#ED6033');
//         }
//         if(jump_num==1){
//             $('#prev').attr('disabled',true).css('background','gray');
//         }
//         if(largest_page==1){
//             $('#next').attr('disabled',true).css('background','gray');
//         }
//         if(jump_num==largest_page){
//             $('#next').attr('disabled',true).css('background','gray');
//         }else{
//             $('#next').attr('disabled',false).css('background','#4B97EB');
//         }
//         page = jump_num;
//         checktable($('#start_year').val(),$('#start_month').val(),$('#start_day').val(),$('#end_year').val(),$('#end_month').val(),$('#end_day').val(),$('.select_week').val());
//     }
// })
// })
//
// // 跳转
// $('#jump').click(function(){
//     if(isNaN($('#jump_val').val())){
//         alert("不是一个数字");
//     }else{
//         var jump_num = $('#jump_val').val(),
//             jump_num = Number(jump_num);
//         var largest_page = $('#largest_page').text(),
//             largest_page = Number(largest_page);
//         var current_page = $('#current_page').text(),
//             current_page = Number(current_page);
//         if(jump_num>largest_page||jump_num<=0){
//             jump_num = current_page;
//             $('#jump_val').val(current_page);
//         }
//         if(jump_num>1){
//             $('#prev').attr('disabled',false).css('background','#ED6033');
//         }
//         if(jump_num==1){
//             $('#prev').attr('disabled',true).css('background','gray');
//         }
//         if(largest_page==1){
//             $('#next').attr('disabled',true).css('background','gray');
//         }
//         if(jump_num==largest_page){
//             $('#next').attr('disabled',true).css('background','gray');
//         }else{
//             $('#next').attr('disabled',false).css('background','#4B97EB');
//         }
//         page = jump_num;
//         checktable(null);
//     }
})












// var href = window.location.href;
// var href = href.split("&");
// var uid = href[3].slice(4);
// var mid = href[4].slice(4);


// function checktable(issearch){
//     var start_year = $('#start_year').val();
//     var start_month = $('#start_month').val();
//     var start_day = $('#start_day').val();
//     var end_year = $('#end_year').val();
//     var end_month = $('#end_month').val();
//     var end_day = $('#end_day').val();
//     // alert(start_year+'----'+start_month);
//     $.ajax({
//          url:ipaddress+"index.php?m=Home&c=User&a=promotionList",
//          data : {
//             "uid" : uid,
//             "mid" : mid,
//             "page_name" : "channelManagement",
//             "search" : issearch,
//             "table" : "user",
//             "page" : page,
//             "start_year":start_year,
//             "start_month":start_month,
//             "start_day": start_day,
//             "end_year":end_year,
//             "end_month":end_month,
//             "end_day":end_day
//          },
//          type: 'POST',
//          dataType: 'json',
//          async: false,
//          success: function (data) {
//             // console.log(data);
//             // console.log(data.data);
//             $('#mytable tbody').empty();
//             $('#total_page').html(`当前<b id="current_page">${data.page.page}</b>/<b id="largest_page">${data.page.totalPage}</b>页`);
//             if(data.data){
//                 for(var i = 0; i<data.data.length; i++){
//                 // <td>${data.data[i].commission1}</td><td>${data.data[i].commission2}</td>
//                     $('#mytable tbody').append(`<tr><td>${data.data[i].uid}</td><td>${data.data[i].username}</td><td>${data.data[i].register_time}</td><td>${data.data[i].recharge}</td><td>${data.data[i].cost}</td><td>${data.data[i].serviceCharge}</td><td>${data.data[i].commission}</td></tr>`)
//                 }
//                 // 分页以及搜索
//                 $('#search').show();
//                 $('#left_page').show();
//                 $('#right_page').show();
//             }else{
//                 alert("没有此查询数据");
//                 $('#left_page').hide();
//                 $('#right_page').hide();
//             }
//         }
//     })
// }

// var page = 1;
// checktable(null);
// // 下一页
// if($('#largest_page').text()==1){
//     $('#next').attr('disabled',true).css('background','gray');
// }
// $('#next').click(function(){
//     page++;
//     $('#prev').attr('disabled',false).css('background','#ED6033');
//     if(page==$('#largest_page').text()){
//         $(this).attr('disabled',true).css('background','gray');
//     }
//     checktable(null);
// })
// // 上一页
// $('#prev').attr('disabled',true).css('background','gray');
// $('#prev').click(function(){
//     page--;
//     if(page==1){
//         $('#prev').attr('disabled',true).css('background','gray');
//     }
//     $('#next').attr('disabled',false).css('background','#4B97EB');
//     checktable(null);
// })

// // 搜索
// $('#search button').click(function(){
//     page = 1;
//     $('#prev').attr('disabled',true).css('background','gray');
//     checktable($('#search input').val());
//     // 若最大页为1，设置下一页不可点
//     if($('#largest_page').text()==1){
//         $('#next').attr('disabled',true).css('background','gray');
//     }else{
//         $('#next').attr('disabled',false).css('background','#4B97EB');
//     }
//     $('#next').unbind("click");
//     $('#prev').unbind("click");
//     $('#jump').unbind("click");
//     $('#next').click(function(){
//         page++;
//         $('#prev').attr('disabled',false).css('background','#ED6033');
//         if(page==$('#largest_page').text()){
//             $(this).attr('disabled',true).css('background','gray');
//         }
//         checktable($('#search input').val());
//     });
//     $('#prev').click(function(){
//         page--;
//         if(page==1){
//             $('#prev').attr('disabled',true).css('background','gray');
//         }
//         $('#next').attr('disabled',false).css('background','#4B97EB');
//         checktable($('#search input').val());
//     });
//     $('#jump').click(function(){
//     if(isNaN($('#jump_val').val())){
//         alert("不是一个数字");
//     }else{
//         var jump_num = $('#jump_val').val(),
//             jump_num = Number(jump_num);
//         var largest_page = $('#largest_page').text(),
//             largest_page = Number(largest_page);
//         var current_page = $('#current_page').text(),
//             current_page = Number(current_page);
//         if(jump_num>largest_page||jump_num<=0){
//             jump_num = current_page;
//             $('#jump_val').val(current_page);
//         }
//         if(jump_num>1){
//             $('#prev').attr('disabled',false).css('background','#ED6033');
//         }
//         if(jump_num==1){
//             $('#prev').attr('disabled',true).css('background','gray');
//         }
//         if(largest_page==1){
//             $('#next').attr('disabled',true).css('background','gray');
//         }
//         if(jump_num==largest_page){
//             $('#next').attr('disabled',true).css('background','gray');
//         }else{
//             $('#next').attr('disabled',false).css('background','#4B97EB');
//         }
//         page = jump_num;
//         checktable($('#search input').val());
//     }
// })
// })

// // 跳转
// $('#jump').click(function(){
//     if(isNaN($('#jump_val').val())){
//         alert("不是一个数字");
//     }else{
//         var jump_num = $('#jump_val').val(),
//             jump_num = Number(jump_num);
//         var largest_page = $('#largest_page').text(),
//             largest_page = Number(largest_page);
//         var current_page = $('#current_page').text(),
//             current_page = Number(current_page);
//         if(jump_num>largest_page||jump_num<=0){
//             jump_num = current_page;
//             $('#jump_val').val(current_page);
//         }
//         if(jump_num>1){
//             $('#prev').attr('disabled',false).css('background','#ED6033');
//         }
//         if(jump_num==1){
//             $('#prev').attr('disabled',true).css('background','gray');
//         }
//         if(largest_page==1){
//             $('#next').attr('disabled',true).css('background','gray');
//         }
//         if(jump_num==largest_page){
//             $('#next').attr('disabled',true).css('background','gray');
//         }else{
//             $('#next').attr('disabled',false).css('background','#4B97EB');
//         }
//         page = jump_num;
//         checktable(null);
//     }
// })
