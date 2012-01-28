//<![CDATA[

var sponsorsBar = new function () {
	
	var space = 50;
	var sponsorsBox;
	
	var move = function (logo) {
		logo.animate({"left": -logo.width()}, (parseInt(logo.css("left"))+logo.width())*15, "linear", function () {
			var prev = $(this).prev();
			if (prev.length < 1) {
				prev = $(this).nextAll().last();
			}
			$(this).css({"left": parseInt(prev.css("left"))+prev.width()+space});
			move(logo);
		});
	}
	
	this.init = function (sponsors) {
		$(function () {
			sponsorsBox = $("#spons .logos");
			
			$(sponsors).each(function () {
				sponsorsBox.append(
					$("<div>").append(
						$("<img>").attr("src", "/gfx/termine/spons/"+this[0]+".png").attr("alt", this[1])
					)
				);
			});
		});
	}
	
	$(window).load(function () {
		var left = 0;
		
		$("div", sponsorsBox).each(function () {
			var _this = $(this);
			_this.css({"left": left});
			left = left + _this.width() + space;
			move(_this);
		});
		
		sponsorsBox.hide().css({"visibility": "visible"}).fadeIn(2500);
	});
	
}

//]]>