@extends('layouts.app')

@section('content')
<script type='text/javascript'>
		window.sites = {!! json_encode($sites) !!};
		console.log(sites);
		window.devices = {!! json_encode($devices) !!};
		console.log(devices);
</script>

<!-- Map Scripting -->
<script>
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
		$.each(window.sites, function(siteIndex, site) {
			var marker = new google.maps.Marker({
				position: {"lat": site.lat, "lng": site.lon},
				map: mapObject.map,
				icon: site.icon,
				optimized: false
			});
			// marker.setIcon(site.icon);
			marker.addListener('click', function() {
				var str =  "<ul>";
				$.each(site.zones, function(zoneIndex, zone) {
					console.log(zone);
					$.each(zone.devices, function(deviceIndex, device) {
						str += "<li class='sensor-" + device.type + "'>" + device.name + ": " + device.readings[device.readings.length - 1] + " " + device.dataScale + "</li>";
					});
				});
				str += "</ul>";
				$(dataDiv).html(str);
			});
		});
	}
</script>
<div class='container dash-container'>
	<div class='row'>
		<h1 class="page-header">Dashboard</h1>
		<div class='col-xs-12 col-md-6 col-lg-6 dash-notices'>
			<h3>Notifications</h3>
			<p>Recommended actions based on data from the past 24 hours</p>
			<ul id='notifications'></ul>
			<div class="container">
				<p class="text-primary"><i class="fa fa-info-circle" aria-hidden="true"></i>  {{\Carbon\Carbon::now()->format('H:i - d/m/y')}}: Root Crops need watering.</p>
				<p class="text-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>  {{\Carbon\Carbon::now()->format('H:i - d/m/y')}}: Greenhouse 3 is too cold.</p>
			</div>
		</div>
		<div class='col-xs-12 col-md-6 col-lg-6 dash-overview'>
			<h3>Overview</h3>
			<p>Status of all devices based on data received in the last 12 hours and when the last connection was established.</p>
			<ul id='notifications'></ul>
			{{-- Device Overview --}}
			<div class="panel-group" id="accordion">
				@foreach ($sites as $site)
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#{{$site['id']}}">{{$site['name']}}</a>
						</h4>
					</div>
					<div id="{{$site['id']}}" class="panel-collapse collapse">
						<div class="panel-body">
							<ul>
								@foreach ($site['zones'] as $zone)
									<li>{{$zone['name']}}<ul>
											@foreach ($zone['devices'] as $device)
													@if ($device->notify === false)
														<li class='text-success'>{{$device->name}} <i class="fa fa-check fa-lg" aria-hidden="true"></i></li>
													@else
														<li class='text-danger'>{{$device->name}} <i class="fa fa-cross fa-lg" aria-hidden="true"></i><li>
													@endif
											@endforeach
										</ul></li>
								@endforeach
							</ul>
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
		<!-- Overview colouring script -->
		<script>

		</script>
	</div>
	<hr>
	<div class='container map-container'>
		<h3>Site Map</h3>
		<div class='row'>
			<div id='iot-map' class='col-md-6'></div>
			<div id='iot-map-data' class='col-md-6'></div>
		</div>
	</div>
	<!-- Google Maps API -->
<!-- 	<script async defer
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAldSd8XQP3VPXbcOQ4smOrpH-rBy3r7O8&callback=initMap">
	</script> -->
	<hr>
	<h3>Devices</h3>
		<div class="panel panel-default">
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
@endsection