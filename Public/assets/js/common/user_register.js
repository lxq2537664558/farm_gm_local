$('#sendMessage').click(function () {
	var phone = $('.phone').val();
	if(phone==""){
		alert("手机号不能为空");
	}else if(!(/^1[3|4|5|8|7][0-9]\d{4,8}$/.test(phone))){
		alert("请填写正确的手机号");
	}else{
		$.ajax({
			url: ipaddress+"sms_send.php",
			type: "POST",
			data: {phone: phone},
			dataType: "json",
			success: function(data){
				console.log(data);
				if(data.msg=="短信发送成功！"){
					$('#sendMessage').attr("disabled", true);
					var nums = 60;
					clock = setInterval(function(){
						nums--;
						if(nums > 0){
						  	$('#sendMessage').val(nums+'秒后可重发');
						}else{
						 	clearInterval(clock); //清除js定时器
							$('#sendMessage').attr("disabled", false);
							$('#sendMessage').val('获取验证码');
							nums = 60; //重置时间
						}
					}, 1000);

					$.ajax({
						url: ipaddress+"index.php?m=Home&c=Common&a=saveCode",
						type: "POST",
						data: {code: data.code, time: data.time},
						dataType: "json",
						success: function(data){
							
						}
					})
				}
			}	
		})
	}
});