$(function () {
	$("#cancelBtn").click(function (event) {
		$(".actions.cancel").slideToggle();
		event.preventDefault();
	});
});