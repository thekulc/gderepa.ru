var myMap, myPosition, panelMap, studioYmap;
var defaultZoom = 16;

ymaps.ready(init);
$(document).ready(function(){
	
});

function init(){
	if ($("#studioPanelYaMap").length > 0){
		var studioPanelYaMapCoords = $("#studioPanelYaMap").attr("data-coords");
		if (studioPanelYaMapCoords.length > 0){
			
			panelMap = new ymaps.Map("studioPanelYaMap",{
				center: studioPanelYaMapCoords.split(","),
				zoom: defaultZoom,
				controls: []
			});
			panelMap.geoObjects.add(getPlaceObj(studioPanelYaMapCoords.split(","), $("#studioPanelYaMap").attr("data-studio-name"), false));
		}
	}
	if ($("#studio-ymap").length > 0){
		var studioYmapCoords = $("#studio-ymap").attr("data-coords");	
		if (studioYmapCoords && studioYmapCoords.length > 0){
			
			studioYmap = new ymaps.Map("studio-ymap",{
				center: studioYmapCoords.split(","),
				zoom: defaultZoom,
				controls: []
			});
			studioYmap.geoObjects.add(getPlaceObj(studioYmapCoords.split(","), $("#studio-ymap").attr("data-studio-name"), false));
		}
	}
	
	if ($("#yaMap").length > 0){
		myMap = new ymaps.Map("yaMap", {
			center: [55.7, 37.6],
			zoom: defaultZoom,
			controls: ['zoomControl', 'geolocationControl', 'searchControl']
		});
		
		myMap.events.add('click', function (e) {
			newPosition = e.get('coords');
			newPlace = getPlaceObj(newPosition, 'Студия здесь');
			myMap.geoObjects.removeAll();
			myMap.geoObjects.add(newPlace);
			setMapValue(newPosition);
		});
		
		ymaps.geolocation.get().then(function (res) {
			myPosition = res.geoObjects.position;
			myPlacemark = getPlaceObj(myPosition, 'Вы здесь');

			myMap.setCenter(myPosition, defaultZoom);
			myMap.geoObjects.add(myPlacemark);
			setMapValue(myPosition);

		}, function (e) {
			console.log(e);
		});
	}
	
}

function getPlaceObj(coords, hint, _draggable = true){
	var place = new ymaps.Placemark(coords, {
        hintContent: hint}, {
    	draggable: _draggable
	});
	place.events.add('dragend', function(e){
		//nCoords = e.get('coords');
		var thisPlacemark = e.get('target');
		nCoords = thisPlacemark.geometry.getCoordinates();
		setMapValue(nCoords);
	});
	return place;
}

function setMapValue(coords){
	$('[name="mapCoords"]').val(coords.toString());
	setAddressValue(coords);
}

function setAddressValue(coords){
	ymaps.geocode(coords).then(function(res){
		var address = [];
		res.geoObjects.each(function(obj){
			address.push(obj.properties.get('text'));
		})
		address = address[0];
		$('[name="address"]').val(address);
	});
	
}
