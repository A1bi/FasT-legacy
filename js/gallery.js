function Gallery(g) {

	var gallery = g;
	var pics = [];
	var cur = -1;
	
	this.addPics = function (p) {
		$.extend(pics, p);
	}
	
	var updatePic = function () {
		var curPic = pics[cur];
		
		$(".pic img").attr("src", "/gfx/cache/gallery/" + gallery + "/medium/" + curPic.id + ".jpg").load(function () {
			$(this).parent().css({width: $(this).width()});
		});
		
		var numbers = $(".bar .number span");
		numbers.eq(0).html(cur+1);
		numbers.eq(1).html(pics.length);
		
		$(".bar .desc").html(curPic.text);
	}
	
	var goNext = function () {
		if (++cur >= pics.length) {
			cur = 0;
		}
		
		updatePic();
	}
	
	var goPrev = function () {
		if (--cur < 0) {
			cur = pics.length - 1;
		}
		
		updatePic();
	}
	
	var registerEvents = function () {
		$(".next").click(goNext);
		$(".prev").click(goPrev);
		
		$(document).keyup(function (event) {
			switch (event.which) {
				case 37:
					goPrev();
					break;
				case 39:
					goNext();
					break;
			}
		});
	}
	
	this.init = function () {
		$(function () {
			$(".pic").append($("<img>").attr("alt", ""));
			registerEvents();
		
			goNext();
		});
	}
}