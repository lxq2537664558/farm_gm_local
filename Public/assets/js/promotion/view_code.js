var href = window.location.href;
var href = href.split("&");
var uid = href[3].slice(4);
$.ajax({
	url : ipaddress+"index.php?m=Home&c=promotion&a=viewCode",
	type: "POST",
	data: {
		'uid' : uid
	},
	dataType: "json",
	success: function(data){
		console.log(data);
		$('.view-code p:nth-child(1) input').val(data.code);
		$('.view-code p:nth-child(2) input').val(data.addUrl);
		$('.view-code p:nth-child(3) img').attr('src',data.url);
	}
})