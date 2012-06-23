$(function () {
	$("#cancelBtn").click(function (event) {
		$(".actions.cancel").slideToggle();
		event.preventDefault();
	});
	
	$(".charges a").click(function (event) {
		$(this).hide().parent().append("<b>bitte warten...</b>");
	});
	
	$(".markPaid").click(function () {
		return confirm("Möchten Sie diese Bestellung wirklich als bezahlt markieren? Der Käufer erhält sofort seine Karten.");
	});
});