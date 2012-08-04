var stats = new function () {
	var stats = {};
	
	var loadStats = function () {
		$.getJSON("?ajax=1&action=getStats", function (data) {
			stats = data;
			
			updateNumbers();
			registerEvents();
		});
	}
	
	var updateNumbers = function () {
		var selection = $("select").val().split(",");
		var orderType = selection[0];
		var additional = selection[1];
		
		var dates = $("table tr:not(.title)");
		dates.each(function (i) {
			if (i >= dates.length-1) {
				date = -1;
			} else {
				date = i+1;
			}
			
			var retail = (orderType == 3) ? additional : 0;
			var dateStats = stats[orderType][retail][date];
			
			$(".type", this).each(function (ticketType) {
				if (orderType != 2) {
					$(this).html(dateStats[ticketType]['number']);
				} else {
					$(this).empty();
				}
			});
			
			var number = dateStats[-1]['number'];
			var revenue = dateStats[-1]['revenue'];
			// subtract number of free tickets
			if (orderType == -1 && additional == 0) {
				number -= stats[2][retail][date][-1]['number'];
				revenue -= stats[2][retail][date][-1]['revenue'];
			}
			
			$(".total", this).html(number);
			$(".revenue", this).html(revenue);
		});
	}
	
	var registerEvents = function () {
		$("select").change(updateNumbers);
	}
	
	var init = function () {
		loadStats();
	}
	
	$(function () {
		init();
	});
}