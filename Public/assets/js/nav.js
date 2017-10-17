$('.am-in').siblings().find('#arrow').removeClass('am-icon-angle-right').addClass('am-icon-angle-down');
$('.admin .admin-parent').click(function(){
    var index = $(this).index();
    if($(this).find('.am-list').hasClass('am-in')){
        $(this).find('#arrow').removeClass('am-icon-angle-down').addClass('am-icon-angle-right');
    }else{
        $(this).find('#arrow').removeClass('am-icon-angle-right').addClass('am-icon-angle-down');
    }
})