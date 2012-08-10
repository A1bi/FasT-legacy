var sponsorsBar = new function () {
	
	var sponsorsBox;
	var cur = -1;
	
	var moveNext = function () {
		var logos = $("div", sponsorsBox);
		var start = 0;
		if (cur != -1) {
			var curBox = logos.eq(cur);
			start = curBox.position().left + curBox.outerWidth();
		}
		if (cur >= logos.length - 1) {
			cur = -1;
		}
		
		var nextBox = logos.eq(++cur);
		nextBox.css("left", start).show();
		var end = nextBox.outerWidth() * -1;
		
		nextBox.animate({left: end}, {
			step: function () {
				if (!$(this).is(".complete") && $(this).position().left + $(this).outerWidth() <= sponsorsBox.width()) {
					$(this).addClass("complete");
					moveNext();
				}
			},
			complete: function () {
				$(this).removeClass("complete");
			},
			duration: (nextBox.position().left + nextBox.outerWidth()) * 15,
			easing: "linear"
		});
	}
	
	this.init = function (sponsors) {
		$(function () {
			sponsorsBox = $("#spons .logos");
			
			$(sponsors).each(function () {
				sponsorsBox.append(
					$("<div>").append(
						$("<img>").attr("src", "/gfx/termine/spons/"+this[0]+".png").attr("alt", this[1])
					).hide()
				);
			});
			
			moveNext();
			
			sponsorsBox.hide().css({"visibility": "visible"}).fadeIn(2500);
		});
	}
	
}