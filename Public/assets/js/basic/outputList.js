var mid=null;
var $index=null;
console.log($('#group').val());
if($('#group').val()==0){
	$('.edit').click(function(){
		alert('您没有此权限');
	})
}else{
	$('.edit').click(function(){
		var $this = $(this);
		$index = $(this).parents('tr').index();
		mid = $this.siblings('td:nth-child(1)').find('input').val();
		$('.edit_area').show();
		$('.edit_area p:nth-child(2) input').val($this.siblings('td:nth-child(1)').find('span').text());
		$('.edit_area p:nth-child(3) input').val($this.siblings('td:nth-child(2)').text());
		$('.edit_area p:nth-child(4) input').val($this.siblings('td:nth-child(3)').text());
		$('.edit_area p:nth-child(5) input').val($this.siblings('td:nth-child(4)').text());
		$('.edit_area p:nth-child(6) input').val($this.siblings('td:nth-child(5)').text());
	})
}


$('.edit_submit').click(function(){
		$.ajax({
			url: ipaddress+"index.php?m=Home&c=Basic&a=editOutputList",
			type: "POST",
			data: {
				mid : mid,
				area : $('.edit_area p:nth-child(2) input').val(),
				productTypeNumber : $('.edit_area p:nth-child(3) input').val(),
				lv1LandProbability : $('.edit_area p:nth-child(4) input').val(),
				lv2LandProbability : $('.edit_area p:nth-child(5) input').val(),
				lv3LandProbability : $('.edit_area p:nth-child(6) input').val()
			},
			dataType: "json",
			success: function(data){
				$('#mytable tbody tr').eq($index).find('td:nth-child(3)').text($('.edit_area p:nth-child(4) input').val());
				$('#mytable tbody tr').eq($index).find('td:nth-child(4)').text($('.edit_area p:nth-child(5) input').val());
				$('#mytable tbody tr').eq($index).find('td:nth-child(5)').text($('.edit_area p:nth-child(6) input').val());
				$('.edit_area').hide();
			}
		})
	})

// 编辑取消
$('#edit_cancel').click(function(){
	$('.edit_area').hide();
})
