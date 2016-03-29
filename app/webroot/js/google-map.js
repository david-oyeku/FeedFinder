var map;
var markers = [];
var markerCluster;
var geoserverUrl = 'http://localhost:8080/geoserver/cite/wms?';
var choroplethMap;
var layerMarker;

$(document).ready(function() {

});

function initMap() {
	map = new google.maps.Map(document.getElementById("map"), {
		center: new google.maps.LatLng(51.2, 7),
		zoom: 5,
		zoomControl: true,
		zoomControlOptions: {
			position: google.maps.ControlPosition.TOP_RIGHT
		},
		mapTypeId: google.maps.MapTypeId.TERRAIN,
	});

	map.addListener('click', function(event){
		event.latLng.lat();
		if(layerMarker != null){
			layerMarker.setMap(null);
		}
		var pg_column = query.category.name.toLowerCase()+query.time.attr_name;
		$.ajax({
			type: 'GET',
			dataType: 'json',
			url: url()+'/map_click',
			data:{
				latitude:event.latLng.lat(),
				longitude:event.latLng.lng(),
				model:query.explore.pg_table,
				pg_column:pg_column
			},
			success: function (result){
				var infowindow = new google.maps.InfoWindow({
					content: result.County[Object.keys(result.County)[0]]+" "+query.category.name+'s'
				});

				layerMarker = new google.maps.Marker({
					position: new google.maps.LatLng(event.latLng.lat(), event.latLng.lng()),
					map: map,
					animation: google.maps.Animation.DROP,
				});
				layerMarker.addListener('click', function() {
					infowindow.open(map, layerMarker);
				});
			},
			error: function (jqXHR, textStatus, errorThrown) {
			}
		});
	});
}

function displayMarkers(venueResult){
	var venue, review, infoText, latLng, marker;
	var infoWindow = new google.maps.InfoWindow();
	venueResult = removeNullAttributes(venueResult);
	for (var i = 0; i < venueResult.length; i++) {
		//check if there are any venues in the result
		venue = venueResult[i]['Venue'];
		infoText = generateHtmlMarkup(venue);
		review = venueResult[i]['Review'];
		latLng = new google.maps.LatLng(venue.latitude,venue.longitude);
		marker = new google.maps.Marker({'position': latLng,map:map});
		delete venue.latitude;
		google.maps.event.addListener(marker, 'click', (function(marker, infoText) {
			return function() {
				infoWindow.setContent(infoText);
				infoWindow.open(map, marker);
			}
		})(marker, infoText));
		markers.push(marker);
	}
	markerCluster = new MarkerClusterer(map, markers);
}

function deleteMarkers() {
	if(markers.length > 0){
		for (var i = 0; i < markers.length; i++) {
			markers[i].setMap(null);
		}
		markers = [];
		markerCluster.clearMarkers();	
	}

}

function generateHtmlMarkup(venue){
	var infoString = "";
	venue = removeNullAttributes(venue);
	for(var i in venue){
		infoString  = infoString+venue[i]+"</br>"
	}
	return infoString;
}


/*
	Loop through each of the attributes and remove the
	ones with null values
	*/
	function removeNullAttributes(venue){
		for(var i in venue){
			if( venue[i] === null || venue[i] === undefined ){
				delete venue[i];
			}
		}
		return venue;
	}


