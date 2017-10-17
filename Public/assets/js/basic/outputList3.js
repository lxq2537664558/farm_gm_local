var mid=null;
var $index=null;
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
		$('.edit_area p:nth-child(7) input').val($this.siblings('td:nth-child(6)').text());
		$('.edit_area p:nth-child(8) input').val($this.siblings('td:nth-child(7)').text());
		$('.edit_area p:nth-child(9) input').val($this.siblings('td:nth-child(8)').text());
		$('.edit_area p:nth-child(10) input').val($this.siblings('td:nth-child(9)').text());
		$('.edit_area p:nth-child(11) input').val($this.siblings('td:nth-child(10)').text());
		$('.edit_area p:nth-child(12) input').val($this.siblings('td:nth-child(11)').text());
		$('.edit_area p:nth-child(13) input').val($this.siblings('td:nth-child(12)').text());
		$('.edit_area p:nth-child(14) input').val($this.siblings('td:nth-child(13)').text());
		$('.edit_area p:nth-child(15) input').val($this.siblings('td:nth-child(14)').text());
		$('.edit_area p:nth-child(16) input').val($this.siblings('td:nth-child(15)').text());
		$('.edit_area p:nth-child(17) input').val($this.siblings('td:nth-child(16)').text());
		$('.edit_area p:nth-child(18) input').val($this.siblings('td:nth-child(17)').text());
		$('.edit_area p:nth-child(19) input').val($this.siblings('td:nth-child(18)').text());
		$('.edit_area p:nth-child(20) input').val($this.siblings('td:nth-child(19)').text());
		$('.edit_area p:nth-child(21) input').val($this.siblings('td:nth-child(20)').text());
		$('.edit_area p:nth-child(22) input').val($this.siblings('td:nth-child(21)').text());
	})
}

$('.edit_submit').click(function(){
	$.ajax({
		url: ipaddress+"index.php?m=Home&c=Basic&a=editOutputList3",
		type: "POST",
		dataType: "json",
		data: {
			mid : mid,
			id : $('.edit_area p:nth-child(2) input').val(),
			name : $('.edit_area p:nth-child(3) input').val(),
			area : $('.edit_area p:nth-child(4) input').val(),
			lv1LandMainProbability : $('.edit_area p:nth-child(5) input').val(),
			lv1LandMainNumber : $('.edit_area p:nth-child(6) input').val(),
			lv1LandMinor1Probability : $('.edit_area p:nth-child(7) input').val(),
			lv1LandMinor1Number : $('.edit_area p:nth-child(8) input').val(),
			lv1LandMinor2Probability : $('.edit_area p:nth-child(9) input').val(),
			lv1LandMinor2Number : $('.edit_area p:nth-child(10) input').val(),
			lv2LandMainProbability : $('.edit_area p:nth-child(11) input').val(),
			lv2LandMainNumber : $('.edit_area p:nth-child(12) input').val(),
			lv2LandMinor1Probability : $('.edit_area p:nth-child(13) input').val(),
			lv2LandMinor1Number : $('.edit_area p:nth-child(14) input').val(),
			lv2LandMinor2Probability : $('.edit_area p:nth-child(15) input').val(),
			lv2LandMinor2Number : $('.edit_area p:nth-child(16) input').val(),
			lv3LandMainProbability : $('.edit_area p:nth-child(17) input').val(),
			lv3LandMainNumber : $('.edit_area p:nth-child(18) input').val(),
			lv3LandMinor1Probability : $('.edit_area p:nth-child(19) input').val(),
			lv3LandMinor1Number : $('.edit_area p:nth-child(20) input').val(),
			lv3LandMinor2Probability : $('.edit_area p:nth-child(21) input').val(),
			lv3LandMinor2Number : $('.edit_area p:nth-child(22) input').val()
		},
		success: function(data){
			$('#mytable tbody tr').eq($index).find('td:nth-child(4)').text($('.edit_area p:nth-child(5) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(5)').text($('.edit_area p:nth-child(6) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(6)').text($('.edit_area p:nth-child(7) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(7)').text($('.edit_area p:nth-child(8) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(8)').text($('.edit_area p:nth-child(9) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(9)').text($('.edit_area p:nth-child(10) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(10)').text($('.edit_area p:nth-child(11) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(11)').text($('.edit_area p:nth-child(12) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(12)').text($('.edit_area p:nth-child(13) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(13)').text($('.edit_area p:nth-child(14) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(14)').text($('.edit_area p:nth-child(15) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(15)').text($('.edit_area p:nth-child(16) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(16)').text($('.edit_area p:nth-child(17) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(17)').text($('.edit_area p:nth-child(18) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(18)').text($('.edit_area p:nth-child(19) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(19)').text($('.edit_area p:nth-child(20) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(20)').text($('.edit_area p:nth-child(21) input').val());
			$('#mytable tbody tr').eq($index).find('td:nth-child(21)').text($('.edit_area p:nth-child(22) input').val());
			$('.edit_area').hide();
		}
	})
})

// 编辑取消
$('#edit_cancel').click(function(){
	$('.edit_area').hide();
})
