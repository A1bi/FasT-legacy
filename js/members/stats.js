var stats = new function () {
	var stats = {};
	var orderType = 0, additional = 0;
	
	var loadStats = function () {
		$.getJSON("?ajax=1&action=getStats", function (data) {
			stats = data;
			
			updateNumbers();
			registerEvents();
		});
	}
	
	var triggerEdit = function () {
		location.href = "?action=editRetail&retail=" + additional;
	}
	
	var updateNumber = function (obj, number) {
		if (number == null) number = 0;
		obj.html(number);
	}
	
	var updateNumbers = function () {
		var selection = $("select").val().split(",");
		orderType = selection[0];
		additional = selection[1];
		
		var retail, slide;
		if (orderType == 3) {
			retail = additional;
			$(".edit").slideDown();
		} else {
			retail = -1;
			$(".edit").slideUp();
		}
		
		var dates = $("table tr:not(.title)");
		dates.each(function (i) {
			if (i >= dates.length-1) {
				date = -1;
			} else {
				date = i+1;
			}
			
			var dateStats = stats[orderType][retail][date];
			var typeBoxes = $(".type", this).empty();
			
			$.each(dateStats, function (ticketType, ticketStats) {
				var index = ticketType - 1;
				if (index < 0) return true;
				updateNumber(typeBoxes.eq(index), ticketStats['number']);
			});
			
			var number = dateStats[-1]['number'];
			var revenue = dateStats[-1]['revenue'];
			// subtract number of free tickets
			if (orderType == -1 && additional == 0) {
				number -= stats[2][retail][date][-1]['number'];
				revenue -= stats[2][retail][date][-1]['revenue'];
			}
			
			updateNumber($(".total", this), number);
			$(".revenue", this).html(revenue);
		});
	}
	
	var registerEvents = function () {
		$("select").change(updateNumbers);
		$(".edit").click(triggerEdit);
	}
	
	var init = function () {
		loadStats();
	}
	
	$(function () {
		init();
	});
}