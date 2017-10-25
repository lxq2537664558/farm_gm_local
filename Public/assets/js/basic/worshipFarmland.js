// var pageSize = 10;    //每页显示的记录条数
// var curPage = 0;        //当前页
// var lastPage;        //最后页
// var direct = 0;        //方向
// var len;            //总行数
// var page;            //总页数
// var begin;
// var end;
// $(document).ready(function display(){
// 	len =$("#mytable tr").length - 1;    // 求这个表的总行数，剔除第一行介绍
// 	page=len % pageSize==0 ? len/pageSize : Math.floor(len/pageSize)+1;//根据记录条数，计算页数
// 	// alert("page==="+page);
// 	curPage=1;    // 设置当前为第一页
// 	displayPage(1);//显示第一页
//
// 	document.getElementById("btn0").innerHTML="当前 " + curPage + "/" + page + " 页    每页 ";    // 显示当前多少页
// 	document.getElementById("sjzl").innerHTML="数据总量 " + len + "";        // 显示数据量
// 	document.getElementById("pageSize").value = pageSize;
//
//
//
// 	$("#btn1").click(function firstPage(){    // 首页
// 		curPage=1;
// 		direct = 0;
// 		displayPage();
// 	});
// 	$("#btn2").click(function frontPage(){    // 上一页
// 		direct=-1;
// 		displayPage();
// 	});
// 	$("#btn3").click(function nextPage(){    // 下一页
// 		direct=1;
// 		displayPage();
// 	});
// 	$("#btn4").click(function lastPage(){    // 尾页
// 		curPage=page;
// 		direct = 0;
// 		displayPage();
// 	});
// 	$("#btn5").click(function changePage(){    // 转页
// 		curPage=document.getElementById("changePage").value * 1;
// 		if (!/^[1-9]\d*$/.test(curPage)) {
// 			alert("请输入正整数");
// 			return ;
// 		}
// 		if (curPage > page) {
// 			alert("超出数据页面");
// 			return ;
// 		}
// 		direct = 0;
// 		displayPage();
// 	});
//
//
// 	$("#pageSizeSet").click(function setPageSize(){    // 设置每页显示多少条记录
// 		pageSize = document.getElementById("pageSize").value;    //每页显示的记录条数
// 		if (!/^[1-9]\d*$/.test(pageSize)) {
// 			alert("请输入正整数");
// 			return ;
// 		}
// 		len =$("#mytable tr").length - 1;
// 		page=len % pageSize==0 ? len/pageSize : Math.floor(len/pageSize)+1;//根据记录条数，计算页数
// 		curPage=1;        //当前页
// 		direct=0;        //方向
// 		displayPage();
// 	});
// });
//
// function displayPage(){
// 	if(curPage <=1 && direct==-1){
// 		direct=0;
// 		alert("已经是第一页了");
// 		return;
// 	} else if (curPage >= page && direct==1) {
// 		direct=0;
// 		alert("已经是最后一页了");
// 		return ;
// 	}
//
// 	lastPage = curPage;
//
// 	// 修复当len=1时，curPage计算得0的bug
// 	if (len > pageSize) {
// 		curPage = ((curPage + direct + len) % len);
// 	} else {
// 		curPage = 1;
// 	}
//
//
// 	document.getElementById("btn0").innerHTML="当前 " + curPage + "/" + page + " 页    每页 ";        // 显示当前多少页
//
// 	begin=(curPage-1)*pageSize + 1;// 起始记录号
// 	end = begin + 1*pageSize - 1;    // 末尾记录号
//
//
// 	if(end > len ) end=len;
// 	$("#mytable tr").hide();    // 首先，设置这行为隐藏
// 	$("#mytable tr").each(function(i){    // 然后，通过条件判断决定本行是否恢复显示
// 		if((i>=begin && i<=end) || i==0 )//显示begin<=x<=end的记录
// 			$(this).show();
// 	});
//  }












// 判断未提交   已提交
for(var i=0; i<$('#mytable tbody tr').length; i++){
	if($('#mytable tbody tr').eq(i).find('td:nth-child(5)').text()==$('#current_number').text()){
		$('#mytable tbody tr').eq(i).find('td:nth-child(6)').text('未提交');
		$('#mytable tbody tr').eq(i).find('td:nth-child(7) button').attr('disabled',false);
		$('#mytable tbody tr').eq(i).find('td:nth-child(7) button').css('background','#00A4FF');
	}else{
		$('#mytable tbody tr').eq(i).find('td:nth-child(6)').text('已提交');
		$('#mytable tbody tr').eq(i).find('td:nth-child(7) button').attr('disabled',true);
		$('#mytable tbody tr').eq(i).find('td:nth-child(7) button').css('background','white');
	}
}

// 删除供奉
var worship_id = $('.worship_id').text();
$('.delete_worship').click(function(){
	var worship_id = $('#mytable tbody tr').eq($(this).parents('tr').index()).find('td:nth-child(1)').text();
	window.location = ipaddress+"index.php?m=Home&c=Basic&a=deleteWorship&area=1&id="+worship_id;
})


// 判断等级是否显示供奉
if($('#group').val()==0){
	$('#edit_page').hide();
}else{
	$('#edit_page').show();
}

// 去添加供奉
$('#goadd_btn').click(function(){
	$('#add_input').show();
})

var change_num = 0;
$('#select_itemid').change(function(){
	if($(this).val()=="请选择供奉物品"){
        return false;
    }
	if(change_num == 0){
		$('#itemid_input').val($(this).val());
		// $('.worship_number').after('<input class="input_number" readonly type="text" name="num" value="" style="width: 80px; margin-left: 10px;">');
		// $('.closing_price').after('<input class="input_closing_price"  type="text" name="num" value="" style="width: 80px; margin-left: 10px;">');
		$('.pricing').after('<input class="input_price" type="text" name="num" value="" style="width: 80px; margin-left: 10px;">');
		// for(var i=1; i<=$('#closing_price_table tr').length; i++){
		// 	if($('#select_itemid').val()==$('#closing_price_table tr:nth-child('+i+')').find("td:nth-child(1)").text()){
		// 		$('.input_closing_price').eq(0).val($('#closing_price_table tr:nth-child('+i+')').find("td:nth-child(2)").text());
		// 	}
		// }
		// // 自动计算数量
		// $('.input_price').eq(0).bind('input propertychange', function() {
		// 	$('.input_number').eq(0).val(Math.ceil($('.input_price').eq(0).val() / $('.input_closing_price').eq(0).val()));
		// });

	}else if(change_num==1){
		$('#itemid_input').val($('#itemid_input').val()+","+$(this).val());
		// $('.input_number').eq(0).after('<input class="input_number" readonly type="text" name="num" value="" style="width: 80px; margin-left: 10px;">');
		// $('.input_closing_price').eq(0).after('<input class="input_closing_price" type="text" name="num" value="" style="width: 80px; margin-left: 10px;">');
		$('.input_price').eq(0).after('<input class="input_price" type="text" name="num" value="" style="width: 80px; margin-left: 10px;">');
		// for(var i=1; i<=$('#closing_price_table tr').length; i++){
		// 	if($('#select_itemid').val()==$('#closing_price_table tr:nth-child('+i+')').find("td:nth-child(1)").text()){
		// 		$('.input_closing_price').eq(1).val($('#closing_price_table tr:nth-child('+i+')').find("td:nth-child(2)").text());
		// 	}
		// }
		// 自动计算数量
		// $('.input_price').eq(0).bind('input propertychange', function() {
		// 	$('.input_number').eq(0).val(Math.ceil($('.input_price').eq(0).val() / $('.input_closing_price').eq(0).val()));
		// });
		//
		// $('.input_price').eq(1).bind('input propertychange', function() {
		// 	$('.input_number').eq(1).val(Math.ceil($('.input_price').eq(1).val() / $('.input_closing_price').eq(1).val()));
		// });
	}else if(change_num==2){
		$(this).attr('disabled',true);
		$('#itemid_input').val($('#itemid_input').val()+","+$(this).val());
		// $('.input_number').eq(1).after('<input class="input_number" readonly type="text" name="num" value="" style="width: 80px; margin-left: 10px;">');
		// $('.input_closing_price').eq(1).after('<input class="input_closing_price" type="text" name="num" value="" style="width: 80px; margin-left: 10px;">');
		$('.input_price').eq(1).after('<input class="input_price" type="text" name="num" value="" style="width: 80px; margin-left: 10px;">');
		// for(var i=1; i<=$('#closing_price_table tr').length; i++){
		// 	if($('#select_itemid').val()==$('#closing_price_table tr:nth-child('+i+')').find("td:nth-child(1)").text()){
		// 		$('.input_closing_price').eq(2).val($('#closing_price_table tr:nth-child('+i+')').find("td:nth-child(2)").text());
		// 	}
		// }
		// 自动计算数量
		// $('.input_price').eq(0).bind('input propertychange', function() {
		// 	$('.input_number').eq(0).val(Math.ceil($('.input_price').eq(0).val() / $('.input_closing_price').eq(0).val()));
		// });
		//
		// $('.input_price').eq(1).bind('input propertychange', function() {
		// 	$('.input_number').eq(1).val(Math.ceil($('.input_price').eq(1).val() / $('.input_closing_price').eq(1).val()));
		// });
		//
		// $('.input_price').eq(2).bind('input propertychange', function() {
		// 	$('.input_number').eq(2).val(Math.ceil($('.input_price').eq(2).val() / $('.input_closing_price').eq(2).val()));
		// });
	}
   change_num++;
});

// 清除选择id
$('#clear_itemid').click(function(){
	change_num=0;
	$('#itemid_input').val("");
	$('#select_itemid').val("请选择供奉物品");
	// $('.input_number').remove();
	// $('.input_closing_price').remove();
	$('.input_price').remove();
	$('#select_itemid').attr('disabled',false);
})

// 取消
$('#cancel_add').click(function(){
	$('#add_input').hide();
})

// 确定添加
$('.add_worship').click(function(){
	var reg = new RegExp("^[0-9]*$");
    var a = true;
    // for(var i = 0; i < $('.input_number').length; i++){
    //     if($('.input_number').eq(i).val()==""){
    //         alert("供奉数量不能为空");
    //         a = false;
    //         return a;
    //     }else if(!reg.test($('.input_number').eq(i).val())){
    //         alert("供奉数量必须为正整数");
    //         a = false;
    //         return a;
    //     }else{
    //         a = true;
    //     }
    // }

    if($('#itemid_input').val()==""){
        alert("供奉物品不能为空");
        a = false;
        return a;
    }
    if($('.worship_days').val()==""){
        alert("供奉天数不能为空");
        a = false;
        return a;
    }
    if(!reg.test($('.worship_days').val())){
        alert("供奉天数必须为正整数");
        a = false;
        return a;
    }

    if(a == true){
		if(change_num==1){
			// $('.worship_number_input').val($('.input_number').eq(0).val());
			// $('.closing_price_input').val($('.input_closing_price').eq(0).val());
			$('.pricing_input').val($('.input_price').eq(0).val());
			// alert($('.pricing_input').val());
	 	}else if(change_num==2){
			// 	$('.worship_number_input').val($('.input_number').eq(0).val()+','+$('.input_number').eq(1).val());
			// 	$('.closing_price_input').val($('.input_closing_price').eq(0).val()+','+$('.input_closing_price').eq(1).val());
		 	$('.pricing_input').val($('.input_price').eq(0).val()+','+$('.input_price').eq(1).val());
	 	}else if(change_num==3){
			// 	$('.worship_number_input').val($('.input_number').eq(0).val()+','+$('.input_number').eq(1).val()+','+$('.input_number').eq(2).val());
			// 	$('.closing_price_input').val($('.input_closing_price').eq(0).val()+','+$('.input_closing_price').eq(1).val()+','+$('.input_closing_price').eq(2).val());
		 	$('.pricing_input').val($('.input_price').eq(0).val()+','+$('.input_price').eq(1).val()+','+$('.input_price').eq(2).val());
	 	}
		return true;
    }
})

var sid = $('#sid').val();
// 提交到服务器
$('#worship_submit').click(function(){
	window.location = ipaddress+"index.php?m=Home&c=Basic&a=worshipSettings&type=1&method=basicWorshipFarmland&sid="+sid;
})



// for(var i=0; i<$('#mytable tbody tr').length; i++){
// 	if($('#mytable tbody tr').eq(i).find('td:nth-child(5)').text()==$('#current_number').text()){
// 		$('#mytable tbody tr').eq(i).find('td:nth-child(6)').text('未提交');
// 		$('#mytable tbody tr').eq(i).find('td:nth-child(7) button').attr('disabled',false);
// 		$('#mytable tbody tr').eq(i).find('td:nth-child(7) button').css('background','#00A4FF');
// 	}else{
// 		$('#mytable tbody tr').eq(i).find('td:nth-child(6)').text('已提交');
// 		$('#mytable tbody tr').eq(i).find('td:nth-child(7) button').attr('disabled',true);
// 		$('#mytable tbody tr').eq(i).find('td:nth-child(7) button').css('background','white');
// 	}
// }

// var worship_id = $('.worship_id').text();
// $('.delete_worship').click(function(){
// 	var worship_id = $('#mytable tbody tr').eq($(this).parents('tr').index()).find('td:nth-child(1)').text();
// 	window.location = ipaddress+"index.php?m=Home&c=Basic&a=deleteWorship&area=1&id="+worship_id;
// })

// if($('#group').val()==0){
// 	$('#edit_page').hide();
// }else{
// 	$('#edit_page').show();
// }

// $('#goadd_btn').click(function(){
// 	$('#add_input').show();
// })

// var change_num = 0;
// $('#select_itemid').change(function(){
// 	if($(this).val()=="请选择供奉物品"){
//         return false;
//     }

// 	if(change_num == 0){
// 		$('#itemid_input').val($(this).val());
// 		$('.worship_number').after('<input class="input_number" type="text" name="num" value="" style="width: 50px; margin-left: 10px;">');
// 	}else if(change_num==1){
// 		$('#itemid_input').val($('#itemid_input').val()+","+$(this).val());
// 		$('.input_number').eq(0).after('<input class="input_number" type="text" name="num" value="" style="width: 50px; margin-left: 10px;">');
// 	}else if(change_num==2){
// 		$(this).attr('disabled',true);
// 		$('#itemid_input').val($('#itemid_input').val()+","+$(this).val());
// 		$('.input_number').eq(1).after('<input class="input_number" type="text" name="num" value="" style="width: 50px; margin-left: 10px;">');
// 	}
//    change_num++;
// });

// $('#clear_itemid').click(function(){
// 	change_num=0;
// 	$('#itemid_input').val("");
// 	$('.input_number').remove();
// 	$('#select_itemid').attr('disabled',false);
// })

// $('#cancel_add').click(function(){
// 	$('#add_input').hide();
// })


// $('.add_worship').click(function(){
// 	var reg = new RegExp("^[0-9]*$");
//     var a = false;
//     for(var i = 0; i < $('.input_number').length; i++){
//         if($('.input_number').eq(i).val()==""){
//             alert("供奉数量不能为空");
//             a = false;
//             return a;
//         }else if(!reg.test($('.input_number').eq(i).val())){
//             alert("供奉数量必须为正整数");
//             a = false;
//             return a;
//         }else{
//             a = true;
//         }
//     }

//     if($('#itemid_input').val()==""){
//         alert("供奉物品不能为空");
//         a = false;
//         return a;
//     }
//     if($('.worship_days').val()==""){
//         alert("供奉天数不能为空");
//         a = false;
//         return a;
//     }
//     if(!reg.test($('.worship_days').val())){
//         alert("供奉天数必须为正整数");
//         a = false;
//         return a;
//     }

//     if(a == true){
// 		if(change_num==1){
// 			$('.worship_number_input').val($('.input_number').eq(0).val());
// 	 	}else if(change_num==2){
// 		 	$('.worship_number_input').val($('.input_number').eq(0).val()+','+$('.input_number').eq(1).val());
// 	 	}else if(change_num==3){
// 		 	$('.worship_number_input').val($('.input_number').eq(0).val()+','+$('.input_number').eq(1).val()+','+$('.input_number').eq(2).val());
// 	 	}
// 	 	return true;
//     }
// })

// var sid = $('#sid').val();
// // 提交到服务器
// $('#worship_submit').click(function(){
// 	window.location = ipaddress+"index.php?m=Home&c=Basic&a=worshipSettings&type=1&method=basicWorshipFarmland&sid="+sid;
// })
