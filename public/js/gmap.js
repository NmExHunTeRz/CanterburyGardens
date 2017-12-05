function initMap() {
    new IoTMap('iot-map', '#iot-map-data');
}

var centerPos = {lat: 51.308340, lng: 1.102324}
var mapOptions = {
	zoom: 16,
	center: centerPos,
	mapTypeControl: false,
		mapTypeId: 'satellite',
	};

    /*
        Create an object containing the JS Google Map at the specified div.
    */
    function IoTMap(mapDiv, dataDiv) {
    	console.log("making map object! " + mapDiv);
    	console.log(document.getElementById(mapDiv));
        this.prevInfoWindow = false;
        this.sites = {};
        this.markers = [];
        //Initialize map object
        this.map = new google.maps.Map(document.getElementById(mapDiv), mapOptions);
        console.log("map object made");
        // Add our sites markers
        addSiteMarkers(this, dataDiv);
    }

    /*
        Add in each site as a marker, and 
    */
    function addSiteMarkers(mapObject, dataDiv) {
        $.each({!! json_encode($sites) !!}, function(siteIndex, site) {
            var marker = new google.maps.Marker({
                position: site.loc,
                map: mapObject.map,
            });
            marker.setIcon(site.icon);
            marker.addListener('click', function() {
                var str =  "<ul>";
                $.each(site.zones, function(zoneIndex, zone) {
                	console.log(zone);
                	$.each(zone.devices, function(deviceIndex, device) {
                    	str += "<li class='sensor-" + device.type + "'>" + device.name + ": " + device.data[device.data.length - 1][1] + "</li>";
                	});
                });
                str += "</ul>";
                $(dataDiv).html(str);
            });
        });
    }