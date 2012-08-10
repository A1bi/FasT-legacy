$(function () {
	$("#cancelBtn").click(function (event) {
		$(".actions.cancel").slideToggle();
		event.preventDefault();
	});
	
	$(".charges a").click(function (event) {
		$(this).hide().parent().append("<b>bitte warten...</b>");
	});
	
	var confirmations = [
		{
			"className": "markPaid",
			"text": "Möchten Sie diese Bestellung wirklich als bezahlt markieren? Der Käufer erhält sofort seine Karten."
		},
		{
			"className": "sendPayReminder",
			"text": "Möchten Sie dem Käufer wirklich eine Zahlungserinnerung senden?"
		},
		{
			"className": "delete",
			"text": "Möchten Sie diese Reservierung wirklich löschen?"
		}
	];
	
	$.each(confirmations, function (key, value) {
		$("."+value.className).click(function () {
			return confirm(value.text);
		});
	});
});