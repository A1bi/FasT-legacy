var order = new function () {
	var steps = ["date", "address", "payment", "confirm", "finish"];
	var info, order;
	
	var goNext = function () {
		if ($(this).is(".prev")) {
			goPrev();
			
		} else {
			if (order.step == 3) {
				order.step++;
				updateBtns();
				placeOrder();
			} else {
				showNext();
			}
		}
	}
	
	var goPrev = function () {
		showPrev();
	}
	
	var showNext = function () {
		updateProgress(1);
		updateStep();
	}
	
	var showPrev = function () {
		updateProgress(-1);
		updateStep();
	}
	
	var updateStep = function () {
		$(".stepCon:visible").slideUp();
		$(".stepCon."+steps[order.step]).slideDown();
		
		if (steps[order.step] == "finish") {
			$(".btns, .progress").slideUp();
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
			if (order.step == 3) {
				action = "bestätigen";
			}
			btnBox.find(".action").html(action);
		}
	}
	
	var toggleBtn = function (btn, toggle) {
		var btnBox = $(".btn."+btn);
		btnBox.toggleClass("disabled", !toggle).unbind();
		
		if (toggle) {
			btnBox.click(goNext);
		}
	}
	
	var updateBtns = function () {
		var ok = false;
		
		switch (order.step) {
			case 0:
				ok = order.total > 0;
				break;
				
			case 1:
				ok = true;
				
				$.each(order.address, function (key, val) {
					if (val == "") {
						ok = false;
						return;
					}
				});
				break;
				
			case 2:
				if (order.payment.method == "transfer") {
					ok = true;
				
				} else {
					ok = true;
					$.each(order.payment, function (key, val) {
						if (val == "" || val === false) {
							ok = false;
							return;
						}
					});
				}
				break;
				
			case 3:
				ok = order.accepted;
				break;
		}
		
		toggleBtn("next", ok);
		toggleBtn("prev", order.step > 0 && order.step < steps.length-1);
		updateBtnAction("next");
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
			list.append(
				$("<li>")
				.append($("<span>").addClass("string").html(date))
				.append($("<span>").addClass("id").html(key))
			);
		});
		
		// prices
		$.each(info.prices, function (key, price) {
			$(".date, .confirm").find("."+key+" .single span").html(price);
		});
		
		registerEvents();
	}
	
	var updateOrder = function () {
		updateStep();
		updateProgress(0);
	}
	
	var choseDate = function () {
		$(this).parent().parent().find(".selected").removeClass("selected");
		$(this).addClass("selected");
		$(".date div.number").slideDown();

		order.date = $(this).parent().find(".id").html();
		$(".stepCon.confirm .date").html($(this).html());
	}
	
	var choseNumber = function () {
		order.number[$(this).attr("name")] = $(this).val();
		updateNumbers();
	}
	
	var updateNumbers = function () {
		order.total = 0;
		var tables = $(".date, .confirm");
		
		$.each(order.number, function (key, number) {
			var total = info.prices[key] * number;
			order.total += total;
			
			var typeBox = tables.find("."+key);
			typeBox.find(".total span").html(total);
			typeBox.find(".number span, span.number").html(number);
		});
		
		tables.find("tr.total .total span").html(order.total);
		$(".stepCon.finish span.total").html(order.total);
		
		updateBtns();
	}
	
	var updateAddress = function () {
		var field = $(this).attr("name");
		var val = $(this).val();
		if (field == "email") {
			var ok = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z]){2,9}$/.test(val);
			if (!ok) {
				val = "";
			}
			
			$(".stepCon.address .error").toggle(!ok);
		}
		order.address[field] = val;
		
		$(".stepCon.confirm tr ."+field).html(val);
			
		updateBtns();
	}
	
	var chosePayment = function () {
		order.payment.method = $(this).val();
		var chargeBox = $(".stepCon.payment .charge");
		var charge = order.payment.method == "charge";
		if (charge) {
			chargeBox.slideDown();
		} else {
			chargeBox.slideUp();
		}
		
		var confirmBox = $(".stepCon.confirm .payment, .stepCon.finish .payment");
		confirmBox.find(".charge").toggle(charge);
		confirmBox.find(".transfer").toggle(!charge);
		
		updateBtns();
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
		
		updateBtns();
	}
	
	var accepted = function () {
		order.accepted = $(this).is(":checked");
		
		updateBtns();
	}
	
	var placeOrder = function () {
		makeRequest({"order": order}, "placeOrder", function (data) {
			if (data.status == "ok") {
				$.extend(order, data.order);
				
				$(".stepCon.finish .sId").html(order.sId);
				updateStep();
			} else {
				alert("error: "+data.error);
			}
		});
	}
	
	var registerEvents = function () {
		$(".stepCon.date li .string").click(choseDate);
		$(".stepCon.date select").change(choseNumber);
		
		$(".stepCon.address input").keyup(updateAddress).focusout(updateAddress);
		
		$(".stepCon.payment input[name=method]").click(chosePayment);
		$(".stepCon.payment").find("input.field, input[name=accepted]").keyup(updatePayment).focusout(updatePayment).click(updatePayment);
		
		$(".stepCon.confirm input").click(accepted);
	}
	
	var init = function () {
		makeRequest({}, "getInfo", function (data) {
			info = data.info;
			order = data.order;
			
			updateInfo();
			updateOrder();
		});
	}
	
	$(function () {
		init();
	});
}