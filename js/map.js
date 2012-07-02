function Map(id) {
	
	var fromProjection = new OpenLayers.Projection("EPSG:4326");
	var toProjection = new OpenLayers.Projection("EPSG:900913");
	var icons = [];
	var locations = [];
	var layers = {};
	var map;
	
	this.registerIcons = function (icns) {
		$.each(icns, function (key, value) {
			var size = new OpenLayers.Size(value.size[0], value.size[1]);
			var offset = new OpenLayers.Pixel(value.offset[0], value.offset[1]);
			var icon = new OpenLayers.Icon(value.file, size, offset);
			
			icons[value.name] = icon;
		});
	}
	
	this.registerLocations = function (locs) {
		$.each(locs, function (key, loc) {
			var location = new OpenLayers.LonLat(loc[0], loc[1]).transform(fromProjection, toProjection);
			locations.push(location);
		});
	}
	
	this.addMarkers = function (markers) {
		$.each(markers, function (key, value) {
			var loc = locations[value.loc];
			
			var marker = new OpenLayers.Marker(loc, icons[value.icon].clone());
			layers.markers.addMarker(marker);
			
			if (value.bubble) {
				var feature = new OpenLayers.Feature(layers.markers, loc); 
				feature.closeBox = true;
				feature.popupClass = OpenLayers.Class(OpenLayers.Popup.AnchoredBubble, {'autoSize': true});
				feature.data.popupContentHTML = "<b>" + value.title + "</b><br />" + value.desc;
				feature.data.overflow = "auto";
			
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
			}
			
		});
	}
	
	this.setCenter = function (loc, zoom) {
		map.setCenter(locations[loc], zoom);
	}
	
	var addLayer = function (layer, name) {
		layers[name] = layer;
		map.addLayer(layer);
	}
	
	
	map = new OpenLayers.Map(id, {
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

	addLayer(new OpenLayers.Layer.OSM(), "osm");
	addLayer(new OpenLayers.Layer.Markers("markers"), "markers");

}