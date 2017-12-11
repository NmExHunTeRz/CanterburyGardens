@extends('layouts.app')
@section('title', 'Dashboard')
@section('header')
<h1>Dashboard</h1>
@endsection

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

<!-- Notifications and status board -->
<div class='container dash-main-container'>
	<div class='row'>
		<div class='col-xs-12 col-md-6 col-lg-6 dash-notices no-pad'>
			<h3 class='dash-title'>Alerts</h3>

			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#notifications-overview">Overview</a></li>
				<li><a data-toggle="tab" href="#notifications-recent">Most Recent</a></li>
			</ul>

			<div class='col-xs-12 col-md-12 col-lg-11 tab-content'>
				<div id='notifications-overview' class="tab-pane fade in active">
					<p>Showing a summary of notifications for all measurements taken over the past 12 hours</p>
					<div class="notifications-container col-xs-12">
						@foreach ($notifications as $notification)
							@foreach ($notification as $type => $alert)
								@php
									$arr = explode('|', $alert);
								@endphp
								@if ($type == 'gas')
									<div class='col-xs-12 notification-item'><img src="/img/gasmask-icon.png" width="25" height="25" alt="gas_sensor"><div class='notification-contents'><p class='notification-site'>{{$arr[0]}}</p><p class="text-danger">{{$arr[1]}}</p></div></div>
								@endif
								@if ($type == 'moisture')
									<div class='col-xs-12 notification-item'><img src="/img/moisture-icon.png" width="25" height="25" alt="gas_sensor"><div class='notification-contents'><p class='notification-site'>{{$arr[0]}}</p><p class="text-primary">{{$arr[1]}}</p></div></div>
								@endif
								@if ($type == 'temp')
								<div class='col-xs-12 notification-item'><img src="/img/thermometer-icon.png" width="25" height="25" alt="gas_sensor"><div class='notification-contents'><p class='notification-site'>{{$arr[0]}}</p><p class="text-primary">{{$arr[1]}}</p></div></div>
								@endif
								@if ($type == 'humidity')
									<div class='col-xs-12 notification-item'><img src="/img/thermometer-icon.png" width="25" height="25" alt="gas_sensor"><div class='notification-contents'><p class='notification-site'>{{$arr[0]}}</p><p class="text-primary">{{$arr[1]}}</p></div></div>
								@endif
								@if ($type == 'lux')
									<div class='col-xs-12 notification-item'><img src="/img/sunny-icon.png" width="25" height="25" alt="gas_sensor"><div class='notification-contents'><p class='notification-site'>{{$arr[0]}}</p><p class="text-primary">{{$arr[1]}}</p></div></div>
								@endif
							@endforeach
						@endforeach
					</div>
				</div>
				<div id='notifications-recent' class="tab-pane fade">
					<!-- TODO: recent notifications go here, in same format as notifications-overview div above -->
					<p>Showing the most recent notifications</p>
					<div class="notifications-container col-xs-12">
						@foreach ($notifications_last as $notification)
							@foreach ($notification as $type => $alert)
								@php
									$arr = explode('|', $alert);
								@endphp
								@if ($type == 'gas')
									<div class='col-xs-12 notification-item'><img src="/img/gasmask-icon.png" width="25" height="25" alt="gas_sensor"><div class='notification-contents'><p class='notification-site'>{{$arr[0]}}</p><p class="text-danger">{{$arr[1]}}</p></div></div>
								@endif
								@if ($type == 'moisture')
									<div class='col-xs-12 notification-item'><img src="/img/moisture-icon.png" width="25" height="25" alt="gas_sensor"><div class='notification-contents'><p class='notification-site'>{{$arr[0]}}</p><p class="text-primary">{{$arr[1]}}</p></div></div>
								@endif
								@if ($type == 'temp')
								<div class='col-xs-12 notification-item'><img src="/img/thermometer-icon.png" width="25" height="25" alt="gas_sensor"><div class='notification-contents'><p class='notification-site'>{{$arr[0]}}</p><p class="text-primary">{{$arr[1]}}</p></div></div>
								@endif
								@if ($type == 'humidity')
									<div class='col-xs-12 notification-item'><img src="/img/thermometer-icon.png" width="25" height="25" alt="gas_sensor"><div class='notification-contents'><p class='notification-site'>{{$arr[0]}}</p><p class="text-primary">{{$arr[1]}}</p></div></div>
								@endif
								@if ($type == 'lux')
									<div class='col-xs-12 notification-item'><img src="/img/sunny-icon.png" width="25" height="25" alt="gas_sensor"><div class='notification-contents'><p class='notification-site'>{{$arr[0]}}</p><p class="text-primary">{{$arr[1]}}</p></div></div>
								@endif
							@endforeach
						@endforeach
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-12 col-lg-6 dash-statuses no-pad">
			<h3 class='dash-title'>Device Statuses</h3>
			<div class='col-xs-12 col-md-12 col-lg-11'>
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
						<div id="{{$site['id']}}" class="panel-collapse collapse panel-body">
							<ul>
								@foreach ($site['zones'] as $zone)
									<li>{{$zone['name']}}
										<ul>
											@foreach ($zone['devices'] as $device)
												@if ($device->notify === false)
													<li class='text-success'>{{$device->name}} <i class="fa fa-check fa-lg" aria-hidden="true"></i></li>
												@else
													<li class='text-danger'>{{$device->name}} <i class="fa fa-times fa-lg" aria-hidden="true"></i><li>
												@endif
											@endforeach
										</ul>
									</li>
								@endforeach
							</ul>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
		<script>
			$accordions = $('#accordion').children().each(function(i) {
				var problem = false;
				$(this).children('.panel-body').children('ul').find('li ul li').each(function(index) {
					if ($(this).hasClass('text-danger')) problem = true;
				});
				if (!problem)
					$(this).children('.panel-heading').addClass('accordion-title-success');
				else 
					$(this).children('.panel-heading').addClass('accordion-title-danger');
			});
		</script>
		<hr>
	</div>
	<hr>
	<div class='row dash-weather-container no-pad text-center'>
		<!-- <h3 class='dash-title'>Weather</h3>
		<div class="container weather-container">
			<div id="cont_Nzk1Nnw1fDN8NXwzfEZGRkZGRnwxfDAwMDAwMHxDfDF8bXBo"><div id="spa_Nzk1Nnw1fDN8NXwzfEZGRkZGRnwxfDAwMDAwMHxDfDF8bXBo"><a id="a_Nzk1Nnw1fDN8NXwzfEZGRkZGRnwxfDAwMDAwMHxDfDF8bXBo" rel="nofollow"  href="http://www.weather-wherever.co.uk/united-kingdom/canterbury_v7956/" target="_blank" style="color:#333;text-decoration:none;">Canterbury Weather forecast</a></div><script type="text/javascript" src="http://widget.weather-wherever.co.uk/js/Nzk1Nnw1fDN8NXwzfEZGRkZGRnwxfDAwMDAwMHxDfDF8bXBo"></script></div> -->
			<!-- Weather widget taken from whateverweather.com -->
		<!-- </div> -->
        <div class="col-sm-4">
            <div class="well well-lg">
                <h3>Past 7 Days' Rainfall:</h3>
                {{$rainfall}} inches
            </div>
        </div>
        <div class="col-sm-4">
            <div class="well well-lg">
                <h3>Today's Weather Will Be:</h3>
                {{$weather['tomorrowWeather']}}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="well well-lg">
                <h3>The Temperature Today Will Be:</h3>
                Day Max: {{$weather['dayTemp']}}°C</br>
                Night Min: {{$weather['nightTemp']}}°C
            </div>
        </div>
        <h3><a href="https://www.metoffice.gov.uk/public/weather/forecast/u10g8x4vg
">For Today's Full Forecast Click Here</a></h3>
	</div>
	<div class='row dash-map-container no-pad'>
		<h3 class='dash-title'>Site Map</h3>
		<div class='container'>
			<div class='row'>
				<div id='iot-map' class='col-xs-12 col-sm-12 col-md-6'></div>
				<div id='iot-map-data' class='col-xs-12 col-sm-12 col-md-6'></div>
			</div>
		</div>
	</div>
	<hr>
	<div class='row dash-chart-container no-pad'>
		<h3 class='dash-title'>Devices</h3>
		<div class="form-group">
			<div class="col-xs-12 col-lg-4">
				<label for="sites-select">Select a site:</label>
				<select class="form-control" id="sites-select">
					@foreach ($sites as $key => $site)
						<option value="{{$key}}">{{$site['name']}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-12 col-lg-4">
				<label for="zones-select">Select a zone:</label>
				<select class="form-control" id="zones-select">
					@php
						$firstSite = array_keys($sites)[0];
					@endphp
					@foreach ($sites[$firstSite]['zones'] as $key => $zone)
						<option value="{{$key}}">{{$zone['name']}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-12 col-lg-4">
				<label for="devices-select">Select a device:</label>
				<select class="form-control" id="devices-select">
					@php
						$firstZone = array_keys($sites[$firstSite]['zones'])[0];
					@endphp
					@foreach($sites[$firstSite]['zones'][$firstZone]['devices'] as $device)
						<option value="{{$device->id}}">{{$device->name}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-sm-2 mt-5 pull-right">
				<button id="chart_button">View Device Data</button>
			</div>
			<div class="col-xs-12">
				<canvas id="myChart"></canvas>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	var chart = null;

	// Site selection dropdown
	$("#sites-select").change(function() {
		setZonesSelection();
		setDevicesSelection();
	});

	$("#zones-select").change(function() {
		setDevicesSelection();
	});

	function setZonesSelection() {
		// Change zones options
		var site_selected = $('#sites-select option:selected');
		var str = "";
		$.each(window.sites[site_selected.val()]['zones'], function(index, zone) {
			str += "<option value='" + index + "'>" + zone.name + "</option>";
		});
		$('#zones-select').html(str);
	}
	function setDevicesSelection() {
		// Change devices options
		var site_selected = $('#sites-select option:selected');
		var zone_selected = $('#zones-select option:selected');
		var str = "";
		$.each(window.sites[site_selected.val()]['zones'][zone_selected.val()]['devices'], function(index, device) {
			str += "<option value='" + device.id + "'>" + device.name + "</option>";
		});
		$('#devices-select').html(str);
	}

	$("#chart_button").click(function() {
		if (chart) // If we have an existing chart then we need to destroy it before generating a new one
			chart.destroy();
		var id = $("#devices-select option:selected").val();
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