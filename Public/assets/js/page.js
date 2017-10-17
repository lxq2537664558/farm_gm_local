$("#mytable tr td").css({'textAlign':'center','height':'40px'});
$("#mytable tr th").css({'textAlign':'center','height':'40px'});

// 分页
var pageSize = 15;    //每页显示的记录条数
var curPage=0;        //当前页
var lastPage;        //最后页
var direct=0;        //方向
var len;            //总行数
var page;            //总页数
var begin;
var end;
	// 设置每页显示多少条记录
$("#pageSizeSet").click(function setPageSize(){    // 设置每页显示多少条记录
    pageSize = document.getElementById("pageSize").value;    //每页显示的记录条数
    if (!/^[1-99999]\d*$/.test(pageSize)) {
        alert("请输入正整数");
        return ;
    }
    if(pageSize>len){
        alert("超出数据总量");
    }
    len =$("#mytable tr").length - 1;
    page=len % pageSize==0 ? len/pageSize : Math.floor(len/pageSize)+1;//根据记录条数，计算页数
    curPage=1;        //当前页
    direct=0;        //方向
    displayPage();
    checkBtn();
});
    
$(document).ready(function display(){   
    len =$("#mytable tr").length - 1;    // 求这个表的总行数，剔除第一行介绍
    page=len % pageSize==0 ? len/pageSize : Math.floor(len/pageSize)+1;//根据记录条数，计算页数
    curPage=1;    // 设置当前为第一页
    displayPage();//显示第一页
    if(page!=1){
    	$('#next-page-btn').css('color','black').attr('disabled',false);
		$('#last-page-btn').css('color','black').attr('disabled',false);
    }

    document.getElementById("current-page").innerHTML= curPage + "/" + page;    // 显示当前多少页
    document.getElementById("data-number").innerHTML=len;        // 显示数据量
    document.getElementById("pageSize").value = pageSize;
	
	
    $("#first-page-btn").click(function firstPage(){    // 首页
        curPage=1;
        direct = 0;
        displayPage();
			checkBtn();
    });
    $("#prev-page-btn").click(function prevPage(){    // 上一页
        direct=-1;
        displayPage();
        checkBtn();
    });
    $("#next-page-btn").click(function nextPage(){    // 下一页
        direct=1;
        displayPage();
        checkBtn();
    });
    $("#last-page-btn").click(function lastPage(){    // 尾页
        curPage=page;
        direct = 0;
        displayPage();
        checkBtn();
    });
    $("#jump-page-btn").click(function changePage(){    // 转页
        curPage=document.getElementById("changePage").value * 1;
        if (!/^[1-9]\d*$/.test(curPage)) {
            alert("请输入正整数");
            return ;
        }
        if (curPage > page) {
            alert("超出数据页面");
            return ;
        }
        direct = 0;
        displayPage();
        checkBtn();
    });

});

function displayPage(){
    lastPage = curPage;

    // 修复当len=1时，curPage计算得0的bug
    if (len > pageSize) {
        curPage = ((curPage + direct + len) % len);
    } else {
        curPage = 1;
    }
    document.getElementById("current-page").innerHTML=curPage + "/" + page;        // 显示当前多少页

    begin=(curPage-1)*pageSize + 1;// 起始记录号
    end = begin + 1*pageSize - 1;    // 末尾记录号

    
    if(end > len ) end=len;
    $("#mytable tr").hide();    // 首先，设置这行为隐藏
    $("#mytable tr").each(function(i){    // 然后，通过条件判断决定本行是否恢复显示
        if((i>=begin && i<=end) || i==0 )//显示begin<=x<=end的记录
            $(this).show();
    });
 }

function checkBtn(){
 	if(curPage < page && curPage!=1) {
    	$('#prev-page-btn').css('color','black').attr('disabled',false);
    	$('#first-page-btn').css('color','black').attr('disabled',false);
    	$('#next-page-btn').css('color','black').attr('disabled',false);
    	$('#last-page-btn').css('color','black').attr('disabled',false);
	}else if(curPage < page && curPage==1){
		$('#prev-page-btn').css('color','gray').attr('disabled',true);
    	$('#first-page-btn').css('color','gray').attr('disabled',true);
    	$('#next-page-btn').css('color','black').attr('disabled',false);
    	$('#last-page-btn').css('color','black').attr('disabled',false);
	}else if(curPage == page && page==1){
		$('#prev-page-btn').css('color','gray').attr('disabled',true);
    	$('#first-page-btn').css('color','gray').attr('disabled',true);
    	$('#next-page-btn').css('color','gray').attr('disabled',true);
    	$('#last-page-btn').css('color','gray').attr('disabled',true);
	}else if(curPage == page && page!=1){
		$('#prev-page-btn').css('color','black').attr('disabled',false);
    	$('#first-page-btn').css('color','black').attr('disabled',false);
    	$('#next-page-btn').css('color','gray').attr('disabled',true);
    	$('#last-page-btn').css('color','gray').attr('disabled',true);
	}
}