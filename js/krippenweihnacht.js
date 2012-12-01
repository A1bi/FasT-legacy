$(function() {
	$(".chooser .item").click(function () {
		var item = $(this);
		if (item.is(".chosen")) return;
		
		item.siblings().removeClass("chosen");
		item.addClass("chosen");
		
		var infos = $(".infos .info");
		infos.removeClass("shown");
		infos.eq(item.index()).addClass("shown");
	});
});