<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Canterbury Gardens</title>
    <style>
		.map-container {
	padding-top: 20px;
	margin: auto;
}

#iot-map {
	height: 400px;
}

/* Data Panel */
#iot-map-data {
	height: 400px;
	background-color: #235789;
}
#iot-map-data ul {
	margin: 15px;
}
#iot-map-data ul li {
	color: white;
	font-size: 18px;
	list-style-type: none;
}

.sensor-gas:before {
	content: "";
	width: 25px;
	height: 25px;
	background: url("/img/gasmask-icon.png");
	background-size: 100%;
	display: inline-block;
	margin-top: 4px;
    left: 40px;
    position: absolute;
}
.sensor-solar:before {
	content: "";
	width: 25px;
	height: 25px;
	background: url("/img/lightning-icon.png");
	background-size: 100%;
	display: inline-block;
	margin-top: 4px;
    left: 40px;
    position: absolute;
}
.sensor-hydrometer:before {
	content: "";
	width: 25px;
	height: 25px;
	background: url("/img/moisture-icon.png");
	background-size: 100%;
	display: inline-block;
	margin-top: 4px;
    left: 40px;
    position: absolute;
}
.sensor-tempHumid:before {
	content: "";
	width: 25px;
	height: 25px;
	background: url("/img/thermometer-icon.png");
	background-size: 100%;
	display: inline-block;
	margin-top: 4px;
    left: 40px;
    position: absolute;
}
.sensor-lumosity:before {
	content: "";
	width: 25px;
	height: 25px;
	background: url("/img/sunny-icon.png");
	background-size: 100%;
	display: inline-block;
	margin-top: 4px;
    left: 40px;
    position: absolute;
}
	</style>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- Charts.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Bootstrap theme -->
    <link rel="stylesheet" href="https://bootswatch.com/3/cosmo/bootstrap.css">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <!-- Google Map -->
<script>
	console.log({!! json_encode($sites) !!});

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
        this.prevInfoWindow = false;
        this.sites = {};
        this.markers = [];
        //Initialize map object
        this.map = new google.maps.Map(document.getElementById(mapDiv), mapOptions);
        // Add our sites markers
        addSiteMarkers(this, dataDiv);
    }

    /*
        Add in each site as a marker, and 
    */
    function addSiteMarkers(mapObject, dataDiv) {
        $.each({!! json_encode($sites) !!}, function(siteIndex, site) {
            var marker = new google.maps.Marker({
                position: {"lat": site.lat, "lng": site.lon},
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
   </script>
</head>
<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    @endif

    <div class="content">
        <div class="title m-b-md">
            Canterbury Gardens
        </div>
    </div>
</div>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">Devices</div>
        <div class="panel-body">
            <div class="form-group">
                <div class="col-sm-10">
                    <label for="devices">Select a device:</label>
                    <select class="form-control" id="devices">
                        @foreach ($devices as $key => $device)
                            <option value="{{$key}}">{{$device->name}} ({{$device->id}})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-success" id="view_device">View</button>
                </div>
                <div class="col-sm-12">
                    <canvas id="myChart" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var chart = null;

        $("#view_device").click(function() {
            if (chart) // If we have an existing chart then we need to destroy it before generating a new one
                chart.destroy();

            var id = $("#devices option:selected").val();

            var encoded_data = {!! json_encode($devices) !!};

            console.log(encoded_data);

            generateLineGraph(encoded_data[id]['readings'], encoded_data[id]['timestamps']);
        });

        function generateLineGraph (data, labels) {
            $("#myChart").html('');
            var ctx = document.getElementById('myChart').getContext('2d');

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '# reading',
                        data: data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }
    });
</script>

    <div class='container map-container'>
        <div class='row'>
            <div id='iot-map' class='col-md-6'></div>
            <div id='iot-map-data' class='col-md-6'></div>
        </div>
    </div>
    <!-- Google Maps API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAldSd8XQP3VPXbcOQ4smOrpH-rBy3r7O8&callback=initMap">
    </script>

</body>
</html>
