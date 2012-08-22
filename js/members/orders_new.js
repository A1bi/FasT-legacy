$(function () {
	$(".type select").change(function () {
		var total = 0;
		
		$(".type select").each(function () {
			total += $(this).val() * $(this).parent().parent().find(".each").html();
		});
		
		$(".total").html(total);
	});
	
	$("input[name=free]").change(function () {
		$(".free").slideToggle();
		$(".nonFree").slideToggle();
	});
});