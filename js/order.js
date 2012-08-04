var order = new function () {
	var steps = ["date", "address", "payment", "confirm", "finish"];
	var info, order;
	
	var goNext = function () {
		if ($(this).is(".disabled")) return;
		
		if ($(this).is(".prev")) {
			showAlert("", false);
			goPrev();
			
		} else {
			// check all info
			var check = checkStep();
			showAlert(check.error, !check.ok);
			if (!check.ok) return;
		
			if (order.step == 3) {
				order.step++;
				updateBtns();
				placeOrder();
			} else {
				updateProgress(1);
				showNext();
			}
		}
	}
	
	var goPrev = function () {
		updateProgress(-1);
		showPrev();
	}
	
	var showNext = function () {
		updateStep(-1);
	}
	
	var showPrev = function () {
		updateStep(1);
	}
	
	var keyUp = function (event) {
		if (event.which == 13) {
			goNext();
		}
	}
	
	var slideToggle = function (obj, toggle) {
		var stepBox = $(".stepBox");
		var stepCon = $(".stepCon."+steps[order.step]);
		
		var props = {
			step: function () {
				stepBox.css({height: stepCon.outerHeight(true)});
			}
		};
		
		if (toggle) {
			obj.slideDown(props);
		} else {
			obj.slideUp(props);
		}
	}
	
	var updateStep = function (moveLeft) {
		// move old step away
		$(".stepCon:visible").animate({left: 100 * moveLeft + "%"}, function () {
			$(this).hide();
		});
		
		// move in new stuff
		var current = $(".stepCon."+steps[order.step]);
		current.show().animate({left: "0%"});
		
		// update height of bounding box
		$(".stepBox").animate({height: current.outerHeight(true)});
		
		if (steps[order.step] == "finish") {
			$(".btns, .progress").fadeTo("default", 0);
		} else {
			updateBtns();
		}
	}
	
	var updateProgress = function (direction) {
		var progress = $(".progress");
		progress.removeClass(steps[order.step]);
		order.step += direction;
		progress.addClass(steps[order.step]);
		$(".progress .current").removeClass("current");
		$(".progress .step").eq(order.step+1).addClass("current");
	}
	
	var updateBtnAction = function (btn) {
		var btnBox = $(".btn."+btn);
		
		if (order.step == 4) {
			btnBox.addClass("loading");
		
		} else {
			var action = "weiter";
			var finish = order.step == 3;
			if (finish) {
				action = "bestätigen";
			}
			btnBox.find(".action").html(action);
			$(".btns").toggleClass("finish", finish);
		}
	}
	
	var toggleBtn = function (btn, toggle) {
		$(".btn."+btn).toggleClass("disabled", !toggle);
	}
	
	var updateBtns = function () {
		toggleBtn("prev", order.step > 0 && order.step < steps.length-1);
		updateBtnAction("next");
	}
	
	var isInt = function (val) {
		return (val.indexOf(".") == -1 && !isNaN(val));
	}
	
	var checkFields = function (fields) {
		var ok = true;
		$.each(fields, function (key, val) {
			if (val == "" && val !== false) {
				ok = false;
				return;
			}
		});
		
		return ok;
	}
	
	var checkStep = function () {
		var ok = true;
		var error;
	
		switch (order.step) {
			case 0:
				if (!order.date) {
					ok = false;
					error = "Bitte wählen Sie einen Termin.";
				} else if (order.total <= 0) {
					ok = false;
					error = "Bitte wählen Sie mindestens eine Karte.";
				} else if (!checkTicketsLeft()) {
					ok = false;
					error = "Bitte beachten Sie den Hinweis!";
				}
				break;
				
			case 1:
				ok = checkFields(order.address);
				if (!ok) {
					error = "Bitte füllen Sie alle Felder aus.";
				} else if (!isInt(order.address['plz']) || order.address['plz'].length < 5) {
					ok = false;
					error = "Die angegebene Postleitzahl ist nicht korrekt.";
				} else if (!/^([a-z0-9-]+\.?)+@([a-z0-9-]+\.)+[a-z]{2,9}$/i.test(order.address['email'])) {
					ok = false;
					error = "Die angegebene e-mail-Adresse ist nicht korrekt.";
				}
				break;
				
			case 2:
				if (order.payment.method == "") {
					ok = false;
					error = "Bitte wählen Sie eine Zahlungsmethode.";
				} else if (order.payment.method == "charge") {
					ok = checkFields(order.payment);
					if (!ok) {
						error = "Bitte füllen Sie alle Felder aus.";
					} else if (!isInt(order.payment['number'])) {
						ok = false;
						error = "Die angegebene Kontonummer ist nicht korrekt.";
					} else if (!isInt(order.payment['blz']) || order.payment['blz'].length < 8) {
						ok = false;
						error = "Die angegebene Bankleitzahl ist nicht korrekt.";
					} else if (!order.payment.accepted) {
						ok = false;
						error = "Bitte akzeptieren Sie die Abbuchung von Ihrem Konto.";
					}
				}
				break;
				
			case 3:
				ok = order.accepted;
				error = "Bitte akzeptieren Sie unsere AGB.";
				break;
		}
		
		return {"ok": ok, "error": error};
	}
	
	var showAlert = function (text, toggle) {
		$(".btns .msg").html(text).toggleClass("alert highlighted", toggle);
	}
	
	var altertHighlightEnded = function () {
		$(this).removeClass("highlighted");
	}
	
	var makeRequest = function (data, action, callback) {
		$.ajax({
			url: "/order.php?ajax=1",
			data: $.extend({"action": action}, data),
			dataType: "json",
			type: "post",
			success: callback
		});
	}
	
	var updateInfo = function () {
		// dates
		var list = $(".date ul");
		$.each(info.dates, function (key, date) {
			var dateItem = $("<li>")
				.append($("<span>").addClass("string").html(date.string))
				.append($("<span>").addClass("id").html(key));
			if (date.ticketsLeft < 1) {
				dateItem.addClass("soldOut").append($("<span>").addClass("msg").html("ausverkauft!"));
			}
			list.append(dateItem);
		});
		
		// prices
		$.each(info.prices, function (key, price) {
			order.number[key] = 0;
			$(".date, .confirm").find("."+price.type+" .single span").html(price.price);
		});
		
		registerEvents();
	}
	
	var updateOrder = function () {
		showNext();
		updateProgress(0);
	}
	
	var choseDate = function () {
		if ($(this).is(".soldOut")) return;
		
		$(this).parent().find(".selected").removeClass("selected");
		$(this).addClass("selected");
		slideToggle($(".date div.number"), true);

		order.date = $(".id", this).html();
		$(".stepCon.confirm .date").html($(this).html());
		
		$(".tooMany span").html(info.dates[order.date]['ticketsLeft']);
		updateTicketsLeft();
	}
	
	var choseNumber = function () {
		var type = $(this).attr("name");
		var number = $(this).val();
		
		$.each(info.prices, function (key, price) {
			if (price.type == type) {
				order.number[key] = parseInt(number);
			}
		});
		updateNumbers();
	}
	
	var updateNumbers = function () {
		order.total = 0;
		order.totalNumber = 0;
		var tables = $(".date, .confirm");
		
		$.each(order.number, function (key, number) {
			var price = info.prices[key];
			var total = price.price * number;
			order.total += total;
			order.totalNumber += number;
			
			var typeBox = tables.find("."+price.type);
			typeBox.find(".total span").html(total);
			typeBox.find(".number span, span.number").html(number);
		});
		
		tables.find("tr.total .total span").html(order.total);
		$(".stepCon.finish span.total").html(order.total);
		
		updateTicketsLeft();
	}
	
	var checkTicketsLeft = function () {
		return !(info.dates[order.date]['ticketsLeft'] < order.totalNumber);
	}
	
	var updateTicketsLeft = function () {
		slideToggle($(".tooMany"), !checkTicketsLeft());
	}
	
	var updateAddress = function () {
		var field = $(this).attr("name");
		var val = $(this).val();
		order.address[field] = val;
		
		$(".stepCon.confirm tr ."+field).html(val);
	}
	
	var chosePayment = function () {
		order.payment.method = $(this).val();
		var charge = order.payment.method == "charge";
		slideToggle($(".stepCon.payment .charge"), charge);
		
		var confirmBox = $(".stepCon.confirm .payment, .stepCon.finish .payment");
		confirmBox.find(".charge").toggle(charge);
		confirmBox.find(".transfer").toggle(!charge);
	}
	
	var updatePayment = function () {
		var val = $(this).val();
		var field = $(this).attr("name");
		if (field == "accepted") {
			val = $(this).is(":checked");
		} else {
			$(".stepCon.confirm .payment .charge ."+field).html(val);
		}
		order.payment[field] = val;
	}
	
	var accepted = function () {
		order.accepted = $(this).is(":checked");
	}
	
	var placeOrder = function () {
		makeRequest({"order": order}, "placeOrder", function (data) {
			if (data.status == "ok") {
				$.extend(order, data.order);
				
				$(".stepCon.finish .sId").html(order.sId);
				showNext();
			} else {
				alert("error: "+data.error);
			}
		});
	}
	
	var registerEvents = function () {
		$(".btn").click(goNext);
		$(".btns .msg").bind("animationend webkitAnimationEnd", altertHighlightEnded);
	
		$(".stepCon.date li").click(choseDate);
		$(".stepCon.date select").change(choseNumber);
		
		$(".stepCon.address").find("select, input").keyup(updateAddress).change(updateAddress);
		
		$(".stepCon.payment input[name=method]").click(chosePayment);
		$(".stepCon.payment").find("input.field, input[name=accepted]").keyup(updatePayment).focusout(updatePayment).click(updatePayment);
		
		$(".stepCon.confirm input").click(accepted);
		
		$(".stepCon").keyup(keyUp);
	}
	
	var init = function () {
		makeRequest({}, "getInfo", function (data) {
			info = data.info;
			order = data.order;
			
			updateInfo();
			updateOrder();
			
			toggleBtn("next", true);
		});
	}
	
	$(function () {
		init();
	});
}