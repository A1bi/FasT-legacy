$(function () {
	$(".double > .row .actor").click(function () {
		$(".double .alt:visible").slideUp();

		$(this).parent().next().slideDown();
	});
	
	$(".double .alt").slideUp();
});