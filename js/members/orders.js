var Search = function (s, r) {
	var resultsBox = r;
	var searchBox = s;
	var currentCriteria = {};
	var currentPage = 1;
	var timer;
	var __this = this;
	
	this.search = function (page) {
		if (!page) {
			page = currentPage;
		} else {
			currentPage = page;
		}
		
		$.getJSON("/mitglieder/buchungen", {action: "search", page: page, search: currentCriteria}, function (data) {
			if (data.pages) {
				showResults(data.results);
				updatePageNav(data.pages);
				var hide = false;
			} else {
				var hide = true;
			}
			
			hideResults(hide);
		});
	}
	
	var hideResults = function (hide) {
		resultsBox.find(".rows").toggle(!hide);
		resultsBox.find(".noRows").toggle(hide);
	}
	
	var showResults = function (results) {
		resultsBox.find("tr.row").remove();
		resultsBox.find("table").append(results);
	}
	
	var criteriaChanged = function () {
		currentCriteria = {};
		searchBox.find("input, select").each(function () {
			var exp = /([a-z]+)\[\]/i;
			var name = $(this).attr("name");
			var val = $(this).val();
			
			var r = exp.exec(name);
			if (r) {
				if ($(this).is(":checked")) {
					if (!currentCriteria[r[1]]) currentCriteria[r[1]] = [];
					currentCriteria[r[1]].push(val);
				}
			
			} else {
				currentCriteria[name] = val;
			}
		});
		
		clearTimeout(timer);
		timer = setTimeout(function () {
			__this.search();
		}, 500);
	}
	
	var updatePageNav = function (pages) {
		var pageBox = $(".pages", resultsBox).empty();

		var gap = false;
		var minDiff = 2;
		for (i = 0; i < pages; i++) {
			var page = i + 1;
			var diff = Math.abs(currentPage - page);
			if (diff <= minDiff || page == 1 || page == pages) {
				var span = $("<span>").html(i+1).click(changePage);
				if (page == currentPage) span.addClass("current");
				pageBox.append(span);
				
				if (page != pages) {
					pageBox.append(", ");
				}
				gap = false;
				
			} else if (!gap) {
				pageBox.append("...");
				gap = true;
			}
			
		}
	}
	
	var changePage = function () {
		var page = $(this).html();
		if (page != currentPage) {
			__this.search(page);
		}
	}
	
	this.setCriteria = function (c) {
		currentCriteria = c;
	}
	
	var registerEvents = function () {
		if (searchBox) {
			$("input[type=text], input[type=tel]", searchBox).keyup(criteriaChanged);
			$("input[type=checkbox], select", searchBox).change(criteriaChanged);
			$(".showMore").click(function () {
				$(this).slideUp();
				$(this).parent().find(".more").slideDown();
			});
		}
	}
	
	$(function () {
		registerEvents();
	});
}

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