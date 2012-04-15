function Map() {
	
	var fromProjection = new OpenLayers.Projection("EPSG:4326");
	var toProjection = new OpenLayers.Projection("EPSG:900913");
	var icons = [];
	var mapData = {};
	var locations = [];
	
	var getMapData = function () {
		$.getJSON("/media/map.json", function (data) {
			mapData = data;
			
			initMap();
		});
	}
	
	var initIcons = function () {
		$.each(mapData.icons, function (key, value) {
			var size = new OpenLayers.Size(value.size[0], value.size[1]);
			var offset = new OpenLayers.Pixel(value.offset[0], value.offset[1]);
			var icon = new OpenLayers.Icon('/gfx/info/'+value.file, size, offset);
			
			icons[value.name] = icon;
		});
	}
	
	var initLocations = function () {
		$.each(mapData.locations, function (key, value) {
			var loc = new OpenLayers.LonLat(value[0], value[1]).transform(fromProjection, toProjection);
			locations.push(loc);
		});
	}
	
	var addMarkers = function (layer, map) {
		$.each(mapData.markers, function (key, value) {
			var loc = locations[value.loc];
			
			var feature = new OpenLayers.Feature(layer, loc); 
			feature.closeBox = true;
			feature.popupClass = OpenLayers.Class(OpenLayers.Popup.FramedCloud, {'autoSize': true});
			feature.data.popupContentHTML = value.text;
			feature.data.overflow = "auto";
		
			var marker = new OpenLayers.Marker(loc, icons[value.icon]);
			marker.feature = feature;
			marker.events.register("mousedown", feature, function (event) {
				if (this.popup == null) {
					this.popup = this.createPopup(this.closeBox);
					map.addPopup(this.popup);
					this.popup.show();
				} else {
					this.popup.toggle();
				}
				OpenLayers.Event.stop(event);
			});

			layer.addMarker(marker);
		});
	}
	
	var initMap = function () {		
		initIcons();
		initLocations();
		var map = new OpenLayers.Map('map', {
				controls: [
					new OpenLayers.Control.Navigation({
						mouseWheelOptions: {
							interval: 50,
							cumulative: false
						}
					}),
					new OpenLayers.Control.Attribution()
				]
		});

		var osm = new OpenLayers.Layer.OSM();
		
		var markers = new OpenLayers.Layer.Markers("markers");
		addMarkers(markers, map);

		map.addLayers([osm, markers]);
		map.setCenter(locations[mapData.centerLoc], mapData.zoom);	
	}
	
	this.init = function () {
		getMapData();
	}
}

function Weather() {
	
	var weatherData = {};
	
	var getMapData = function () {
		$.getJSON("ajax.php", {action: "weather"}, function (data) {
			weatherData = data.data;
			
			initWeather();
		});
	}
	
	var initWeather = function () {
		var wBox = $(".weather");
		
		var image = $("<img />").attr("src", "http://l.yimg.com/a/i/us/nws/weather/gr/"+weatherData.code+weatherData.daytime+".png");
		$(".icon", wBox).append(image);
		var weatherBox = $(".weather");
		
		$.each(weatherData, function (key, value) {
			$("."+key, weatherBox).html(value);
		});
		
		image.load(function () {
			$(".loader", wBox).fadeOut(function () {
				$(".info", wBox).fadeIn();
			});
		});
	}
	
	this.init = function () {
		getMapData();
	}
	
}

$(function () {
	var map = new Map();
	map.init();
	
	var weather = new Weather();
	weather.init();
});