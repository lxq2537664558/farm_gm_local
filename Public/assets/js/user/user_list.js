console.log(ipaddress);
function checktable(issearch){
    $.ajax({
         url: ipaddress+"index.php?m=Home&c=User&a=index",
         data : {
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
                    $('#mytable tbody').append(`<tr><td><span>${data.data[i].id}</span><input type="hidden" name="" value=${data.data[i].mid}></td><td>${data.data[i].username}</td><td>${data.data[i].phone}</td><td>${data.data[i].idcard}</td><td>${data.data[i].state}</td><td>${data.data[i].father_id}</td><td>${data.data[i].user_type}</td><td>${data.data[i].gold}</td><td>${data.data[i].login_time}</td><td>${data.data[i].register_time}</td></tr>`)
                }

                // 添加操作菜单
                for(var i=0; i<data.data.length+1; i++){
                    // 判断用户是否拥有 推广列表
                    if($('#mytable tbody tr:nth-child('+i+')').find('td:nth-child(7)').text()!="普通用户"){
                        $('#mytable tbody tr:nth-child('+i+')').append('<td><span class="edit" style="color: blue; cursor: pointer; margin-right: 10px;">编辑</span><span class="delete_user" style="color: blue; cursor: pointer; margin-right: 10px;">删除</span><span class="pending_order" style="color: blue; cursor: pointer; margin-right: 10px;">挂单记录</span><span class="gold_records" style="color: blue; cursor: pointer; margin-right: 10px;">金币记录</span><span class="promotion_list" style="color: blue; cursor: pointer; margin-right: 10px;">推广列表</span><span class="items_list" style="color: blue; cursor: pointer;">物品清单</span><span class="banned" style="color: blue; cursor: pointer; margin-left: 10px;">禁言</span></td>');
                    }else{
                        $('#mytable tbody tr:nth-child('+i+')').append('<td><span class="edit" style="color: blue; cursor: pointer; margin-right: 10px;">编辑</span><span class="delete_user" style="color: blue; cursor: pointer; margin-right: 10px;">删除</span><span class="pending_order" style="color: blue; cursor: pointer; margin-right: 10px;">挂单记录</span><span class="gold_records" style="color: blue; cursor: pointer; margin-right: 10px;">金币记录</span><span class="items_list" style="color: blue; cursor: pointer;">物品清单</span><span class="banned" style="color: blue; cursor: pointer; margin-left: 10px;">禁言</span></td>');
                    }
                }


                // 分页以及搜索
                $('#search').show();
                $('#left_page').show();
                $('#right_page').show();

                // 用户编辑
                if($('#group').val()!=2 && $('#group').val()!=3){
                    $('.edit').hide();
                    $('.edit').click(function(){
                        alert('您没有此权限');
                    })
                }else{
                    $('.edit').show();
                    $('.edit').click(function(){
                        var uid = $(this).parents('tr').find('td:nth-child(1) span').text();
                        $.ajax({
                            url: ipaddress+`index.php?m=Home&c=User&a=editUser&uid=${uid}`,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data){
                                // 创建编辑html
                                var edit_html = '<h3 style="margin-top: 10px; text-align: center;">用户编辑</h3>'+
                                '<p class="change_uid"><span style="display: inline-block; width: 80px; text-align: right; margin-right: 20px;">用户uid</span><input type="" name="" readonly="readonly"></p>'+
                                '<p class="change_username"> <span style="display: inline-block; width: 80px; text-align: right; margin-right: 20px;">用户名</span><input type="" name=""></p>'+
                                '<p class="change_password"><span style="display: inline-block; width: 80px; text-align: right; margin-right: 20px;">设置密码</span><input type="password" name="" placeholder="若不更改密码请勿填写"></p>'+
                                '<p class="change_type"><span style="display: inline-block; width: 80px; text-align: right; margin-right: 20px;">用户组</span><select class="type_select"><option value="putong">普通用户</option><option value="daili">代理商</option><option value="huizhang">商会长</option></select></p>'+
                                '<p class="change_state"><span style="display: inline-block; width: 80px; text-align: right; margin-right: 20px;">状态</span><select class="state_select"><option value="zhengchang">正常</option><option value="fenghao">封号</option><option value="yongjiu">永久封号</option></select></p><p style="display: none;" class="seal_time"><span style="display: inline-block; width: 80px; text-align: right; margin-right: 20px;">封号时间</span><input type="" name="" placeholder="请输入封号天数"></p>'+
                                '<div class="btn" style="padding-left: 80px;"><button style="width: 80px; height: 30px; margin-right: 20px; border-radius: 5px; border: none; margin-bottom: 20px;" class="sure_change">确定</button><button style="width: 80px; height: 30px; border-radius: 5px; border: none; margin-bottom: 20px;" class="cancel_change">取消</button></div>';

                                $('.user_edit').html(edit_html);

                                $('.user_edit').find('.change_uid input').val(data.id);
                                $('.user_edit').find('.change_username input').val(data.username);
                                $('.user_edit').find('.change_username input').attr('readonly',true);
                                // $('.user_edit').find('.change_idcard input').val(data.idcard);
                                if(data.user_type==0){
                                    $('.type_select').val('putong');
                                }else if(data.user_type==1){
                                    $('.type_select').val('daili');
                                }else if(data.user_type==2){
                                    $('.type_select').val('huizhang');
                                }
                                if(data.state==0){
                                    $('.state_select').val('zhengchang');
                                    $('.seal_time').hide()
                                }else if(data.state==1){
                                    $('.state_select').val('fenghao');
                                    $('.seal_time').show().find('input').val(data.close_time);
                                }else if(data.state==2){
                                    $('.state_select').val('yongjiu');
                                    $('.seal_time').show().find('input').val(data.close_time);
                                }
                                // 封号选择
                                $('.state_select').change(function(){
                                    if($('.state_select').val()=="zhengchang" || $('.state_select').val()=="yongjiu"){
                                        $('.seal_time').hide();
                                    }else{
                                        $('.seal_time').show();
                                    }
                                })

                                 // 点击取消更改
                                $('.cancel_change').click(function(){
                                    $('.user_edit').empty();
                                    // alert(1);
                                });


                                // 点击确定更改
                                $('.sure_change').click(function(){
                                   // console.log(1);
                                    var user_type = 0;
                                    var state = 0;
                                    if($('.change_type .type_select').val()=="putong"){
                                        user_type = 0;
                                    }else if($('.change_type .type_select').val()=="daili"){
                                        user_type = 1;
                                    }else if($('.change_type .type_select')
                                        .val()=="huizhang"){
                                        user_type = 2;
                                    }
                                    if($('.change_state .state_select').val()=="zhengchang"){
                                        state = 0;
                                    }else if($('.change_state .state_select').val()=="fenghao"){
                                        state = 1;
                                    }else if($('.change_state .state_select').val()=="yongjiu"){
                                        state = -1;
                                    }
                                    $.ajax({
                                        url : ipaddress+"index.php?m=Home&c=User&a=editUser",
                                        data: {
                                            uid : $('.change_uid input').val(),
                                            username: $('.change_username input').val(),
                                            // idcard : $('.change_idcard input').val(),
                                            password : $('.change_password input').val(),
                                            user_type : user_type,
                                            state : state,
                                            close_time : $('.seal_time input').val()
                                        },
                                        dataType: "json",
                                        type: "POST",
                                        success: function(data){
                                            alert(data.msg);
                                            // $('.user_edit').hide();
                                            if(data.msg=="修改成功！"){
                                                // $('.user_edit').hide();
                                                $('.user_edit').html("");
                                            }
                                        }
                                    })
                                })
                            }
                        })
                    })
                }

                // 用户删除
                if($('#group').val()!=2){
                    $('.delete_user').hide();
                    $('.delete_user').click(function(){
                        alert('您没有此权限');
                    })
                }else{
                    $('.delete_user').click(function(){
                        var r = confirm("确定删除吗");
                        if(r == true) {
                            var uid = $(this).parents('tr').find('td:nth-child(1) span').text();
                            var mid = $(this).parents('tr').find('td:nth-child(1) input').val();
                            window.location=ipaddress+`index.php?m=Home&c=User&a=deleteUser&uid=${uid}`
                        }else {
                            return false;
                        }
                    })
                }

                // 用户挂单记录
                $('.pending_order').click(function(){
                    var uid = $(this).parents('tr').find('td:nth-child(1) span').text();
                    var mid = $(this).parents('tr').find('td:nth-child(1) input').val();
                    window.location=ipaddress+`index.php?m=Home&c=User&a=pendingOrder&uid=${uid}&mid=${mid}`
                })

                // 用户金币记录
                $('.gold_records').click(function(){
                    var uid = $(this).parents('tr').find('td:nth-child(1) span').text();
                    var mid = $(this).parents('tr').find('td:nth-child(1) input').val();
                    window.location=ipaddress+`index.php?m=Home&c=User&a=goldRecords&uid=${uid}&mid=${mid}`
                })

                // 推广列表
                $('.promotion_list').click(function(){
                    var uid = $(this).parents('tr').find('td:nth-child(1) span').text();
                    var mid = $(this).parents('tr').find('td:nth-child(1) input').val();
                    window.location=ipaddress+`index.php?m=Home&c=User&a=promotionList&uid=${uid}&mid=${mid}`
                })

                //  物品清单
                $('.items_list').click(function(){
                    var uid = $(this).parents('tr').find('td:nth-child(1) span').text();
                    var mid = $(this).parents('tr').find('td:nth-child(1) input').val();
                    window.location=ipaddress+`index.php?m=Home&c=User&a=itemsList&uid=${uid}&mid=${mid}`
                })

                // 禁言
                $('.banned').click(function(){
                    var uid = $(this).parents('tr').find('td:nth-child(1) span').text();
                    var mid = $(this).parents('tr').find('td:nth-child(1) input').val();
                    window.location=ipaddress+`index.php?m=Home&c=User&a=banned&uid=${uid}&mid=${mid}`
                })
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






// function checktable(issearch){
//     $.ajax({
//         url:ipaddress+"index.php?m=Home&c=User&a=index",
//         data : {
//             "page_name" : "channelManagement",
//             "search" : issearch,
//             "table" : "user",
//             "page" : page
//         },
//         type: 'POST',
//         dataType: 'json',
//         async: false,
//         success: function (data) {
//             // console.log(data);
//             // console.log(data.data);
//             $('#mytable tbody').empty();
//             $('#total_page').html(`当前<b id="current_page">${data.page.page}</b>/<b id="largest_page">${data.page.totalPage}</b>页`);
//             if(data.data){
//                 for(var i = 0; i<data.data.length; i++){
//                     $('#mytable tbody').append(`<tr><td><span>${data.data[i].id}</span><input type="hidden" name="" value=${data.data[i].mid}></td><td>${data.data[i].username}</td><td>${data.data[i].phone}</td><td>${data.data[i].idcard}</td><td>${data.data[i].state}</td><td>${data.data[i].father_id}</td><td>${data.data[i].grandfather_id}</td><td>${data.data[i].user_type}</td><td>${data.data[i].gold}</td><td>${data.data[i].login_time}</td><td>${data.data[i].register_time}</td></tr>`)
//                 }

//                 // 添加操作菜单
//                 for(var i=0; i<data.data.length+1; i++){
//                     // 判断用户是否拥有 推广列表
//                     if($('#mytable tbody tr:nth-child('+i+')').find('td:nth-child(8)').text()!="普通用户"){
//                         $('#mytable tbody tr:nth-child('+i+')').append('<td><span class="edit" style="color: blue; cursor: pointer; margin-right: 10px;">编辑</span><span class="delete_user" style="color: blue; cursor: pointer; margin-right: 10px;">删除</span><span class="pending_order" style="color: blue; cursor: pointer; margin-right: 10px;">挂单记录</span><span class="gold_records" style="color: blue; cursor: pointer; margin-right: 10px;">金币记录</span><span class="promotion_list" style="color: blue; cursor: pointer; margin-right: 10px;">推广列表</span><span class="items_list" style="color: blue; cursor: pointer;">物品清单</span></td>');
//                     }else{
//                         $('#mytable tbody tr:nth-child('+i+')').append('<td><span class="edit" style="color: blue; cursor: pointer; margin-right: 10px;">编辑</span><span class="delete_user" style="color: blue; cursor: pointer; margin-right: 10px;">删除</span><span class="pending_order" style="color: blue; cursor: pointer; margin-right: 10px;">挂单记录</span><span class="gold_records" style="color: blue; cursor: pointer; margin-right: 10px;">金币记录</span><span class="items_list" style="color: blue; cursor: pointer;">物品清单</span></td>');
//                     }
//                 }


//                 // 分页以及搜索
//                 $('#search').show();
//                 $('#left_page').show();
//                 $('#right_page').show();

//                 // 用户编辑
//                 if($('#group').val()!=2){
//                     $('.edit').hide();
//                     $('.edit').click(function(){
//                         alert('您没有此权限');
//                     })
//                 }else{
//                     $('.edit').show();
//                     $('.edit').click(function(){
//                         var uid = $(this).parents('tr').find('td:nth-child(1) span').text();
//                         $.ajax({
//                             url: ipaddress+`index.php?m=Home&c=User&a=editUser&uid=${uid}`,
//                             type: 'GET',
//                             dataType: 'json',
//                             success: function(data){
//                                 console.log(data);
//                                 $('.user_edit').show();
//                                 $('.user_edit').find('#change_uid input').val(data.id);
//                                 $('.user_edit').find('#change_username input').val(data.username);
//                                 $('.user_edit').find('#change_idcard input').val(data.idcard);
//                                 if(data.user_type==0){
//                                     $('#type_select').val('putong');
//                                 }else if(data.user_type==1){
//                                     $('#type_select').val('daili');
//                                 }else if(data.user_type==2){
//                                     $('#type_select').val('huizhang');
//                                 }
//                                 if(data.state==0){
//                                     $('#state_select').val('zhengchang');
//                                     $('#seal_time').hide()
//                                 }else if(data.state==1){
//                                     $('#state_select').val('fenghao');
//                                     $('#seal_time').show().find('input').val(data.close_time);
//                                 }else if(data.state==2){
//                                     $('#state_select').val('yongjiu');
//                                     $('#seal_time').show().find('input').val(data.close_time);
//                                 }
//                                 // 封号选择
//                                 $('#state_select').change(function(){
//                                     if($('#state_select').val()=="zhengchang"){
//                                         $('#seal_time').hide();
//                                     }else{
//                                         $('#seal_time').show();
//                                     }
//                                 })
//                                 // 点击取消更改
//                                 $('#cancel_change').click(function(){
//                                     $('.user_edit').hide();
//                                 })
//                                 // 点击确定更改
//                                 $('#sure_change').click(function(){
//                                     var user_type = 0;
//                                     var state = 0;
//                                     if($('#change_type #type_select').val()=="putong"){
//                                         user_type = 0;
//                                     }else if($('#change_type #type_select').val()=="daili"){
//                                         user_type = 1;
//                                     }else if($('#change_type #type_select')
//                                             .val()=="huizhang"){
//                                         user_type = 2;
//                                     }
//                                     if($('#change_state #state_select').val()=="zhengchang"){
//                                         state = 0;
//                                     }else if($('#change_state #state_select').val()=="fenghao"){
//                                         state = 1;
//                                     }else if($('#change_state #state_select').val()=="yongjiu"){
//                                         state = -1;
//                                     }
//                                     $.ajax({
//                                         url : ipaddress+"index.php?m=Home&c=User&a=editUser",
//                                         data: {
//                                             uid : $('#change_uid input').val(),
//                                             username: $('#change_username input').val(),
//                                             idcard : $('#change_idcard input').val(),
//                                             password : $('#change_password input').val(),
//                                             user_type : user_type,
//                                             state : state,
//                                             close_time : $('#seal_time input').val()
//                                         },
//                                         dataType: "json",
//                                         type: "POST",
//                                         success: function(data){
//                                             alert(data.msg);
//                                             if(data.msg=="修改成功！"){
//                                                 $('.user_edit').hide();
//                                             }
//                                         }
//                                     })
//                                 })
//                             }
//                         })
//                     })
//                 }

//                 // 用户删除
//                 if($('#group').val()!=2){
//                     $('.delete_user').hide();
//                     $('.delete_user').click(function(){
//                         alert('您没有此权限');
//                     })
//                 }else{
//                     $('.delete_user').click(function(){
//                         var r = confirm("确定删除吗");
//                         if(r == true) {
//                             var uid = $(this).parents('tr').find('td:nth-child(1) span').text();
//                             var mid = $(this).parents('tr').find('td:nth-child(1) input').val();
//                             window.location=ipaddress+`index.php?m=Home&c=User&a=deleteUser&uid=${uid}`
//                         }else {
//                             return false;
//                         }
//                     })
//                 }

//                 // 用户挂单记录
//                 $('.pending_order').click(function(){
//                     var uid = $(this).parents('tr').find('td:nth-child(1) span').text();
//                     var mid = $(this).parents('tr').find('td:nth-child(1) input').val();
//                     window.location=ipaddress+`index.php?m=Home&c=User&a=pendingOrder&uid=${uid}&mid=${mid}`
//                 })

//                 // 用户金币记录
//                 $('.gold_records').click(function(){
//                     var uid = $(this).parents('tr').find('td:nth-child(1) span').text();
//                     var mid = $(this).parents('tr').find('td:nth-child(1) input').val();
//                     window.location=ipaddress+`index.php?m=Home&c=User&a=goldRecords&uid=${uid}&mid=${mid}`
//                 })

//                 // 推广列表
//                 $('.promotion_list').click(function(){
//                     var uid = $(this).parents('tr').find('td:nth-child(1) span').text();
//                     var mid = $(this).parents('tr').find('td:nth-child(1) input').val();
//                     window.location=ipaddress+`index.php?m=Home&c=User&a=promotionList&uid=${uid}&mid=${mid}`
//                 })

//                 //  物品清单
//                 $('.items_list').click(function(){
//                     var uid = $(this).parents('tr').find('td:nth-child(1) span').text();
//                     var mid = $(this).parents('tr').find('td:nth-child(1) input').val();
//                     window.location=ipaddress+`index.php?m=Home&c=User&a=itemsList&uid=${uid}&mid=${mid}`
//                 })
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
// $('#next').attr('disabled',true).css('background','gray');
// }
// $('#next').click(function(){
// page++;
// $('#prev').attr('disabled',false).css('background','#ED6033');
// if(page==$('#largest_page').text()){
//     $(this).attr('disabled',true).css('background','gray');
// }
// checktable(null);
// })
// // 上一页
// $('#prev').attr('disabled',true).css('background','gray');
// $('#prev').click(function(){
// page--;
// if(page==1){
//     $('#prev').attr('disabled',true).css('background','gray');
// }
// $('#next').attr('disabled',false).css('background','#4B97EB');
// checktable(null);
// })

// // 搜索
// $('#search button').click(function(){
// page = 1;
// $('#prev').attr('disabled',true).css('background','gray');
// checktable($('#search input').val());
// // 若最大页为1，设置下一页不可点
// if($('#largest_page').text()==1){
//     $('#next').attr('disabled',true).css('background','gray');
// }else{
//     $('#next').attr('disabled',false).css('background','#4B97EB');
// }
// $('#next').unbind("click");
// $('#prev').unbind("click");
// $('#jump').unbind("click");
// $('#next').click(function(){
//     page++;
//     $('#prev').attr('disabled',false).css('background','#ED6033');
//     if(page==$('#largest_page').text()){
//         $(this).attr('disabled',true).css('background','gray');
//     }
//     checktable($('#search input').val());
// });
// $('#prev').click(function(){
//     page--;
//     if(page==1){
//         $('#prev').attr('disabled',true).css('background','gray');
//     }
//     $('#next').attr('disabled',false).css('background','#4B97EB');
//     checktable($('#search input').val());
// });
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
//         checktable($('#search input').val());
//     }
// })
// })

// // 跳转
// $('#jump').click(function(){
// if(isNaN($('#jump_val').val())){
//     alert("不是一个数字");
// }else{
//     var jump_num = $('#jump_val').val(),
//         jump_num = Number(jump_num);
//     var largest_page = $('#largest_page').text(),
//         largest_page = Number(largest_page);
//     var current_page = $('#current_page').text(),
//         current_page = Number(current_page);
//     if(jump_num>largest_page||jump_num<=0){
//         jump_num = current_page;
//         $('#jump_val').val(current_page);
//     }
//     if(jump_num>1){
//         $('#prev').attr('disabled',false).css('background','#ED6033');
//     }
//     if(jump_num==1){
//         $('#prev').attr('disabled',true).css('background','gray');
//     }
//     if(largest_page==1){
//         $('#next').attr('disabled',true).css('background','gray');
//     }
//     if(jump_num==largest_page){
//         $('#next').attr('disabled',true).css('background','gray');
//     }else{
//         $('#next').attr('disabled',false).css('background','#4B97EB');
//     }
//     page = jump_num;
//     checktable(null);
// }
// })
