@extends('layouts.app')
@section('title', 'Graphs')
@section('header')
<h1>Graphs</h1>
@endsection

@section('content')
<script type='text/javascript'>
	window.sites = {!! json_encode($sites) !!};
</script>
<div class='container graphs-container'>
	<h3>Showing data averages from {{\Carbon\Carbon::today()->subWeek()->toDateString()}} to {{\Carbon\Carbon::today()->toDateString()}} at each site.</h3>
	<div class='row' id='selection'>
		<h3 class='dash-title'>Data Selection</h3>
		<div class='form-group' id='graphs-nav'>
			<div class='col-xs-12 col-lg-9'>
				<label for="gph-sites-select">Select a site:</label>
				<select class="form-control" id="gph-sites-select">
					@foreach ($sites as $key => $site)
						@if ($key === 'outside')
							@foreach($site['zones'] as $zoneKey => $zone)
								<option value="{{$site['id']}}|{{$zoneKey}}">{{$zone['name']}}</option>
							@endforeach
						@else
							<option value="{{$key}}">{{$site['name']}}</option>
						@endif
					@endforeach
				</select>
			</div>
			<div class='col-xs-12 col-lg-3'>
				<button id='gph-button'>View Site Data</button>
			</div>
		</div>
	</div>
	<hr>
	<div class='row'>
		<div class='col-xs-12 col-lg-6' id='light'>
			<h3 class='dash-title'>Average Light Levels</h3>
			<canvas id='light-chart'></canvas>
			<div id='light-data'></div>
		</div>
		<div class='col-xs-12 col-lg-6' id='moisture'>
			<h3 class='dash-title'>Average Moisture Measurements</h3>
			<canvas id='moisture-chart'></canvas>
			<div id='moisture-data'></div>
		</div>
		<div class='col-xs-12 col-lg-6' id='temperature'>
			<h3 class='dash-title'>Average Temperature Measurements</h3>
			<canvas id='temperature-chart'></canvas>
			<div id='temperature-data'></div>
		</div>
		<div class='col-xs-12 col-lg-6' id='humidity'>
			<h3 class='dash-title'>Average Humidity Measurements</h3>
			<canvas id='humidity-chart'></canvas>
			<div id='humidity-data'></div>
		</div>
		<div class='col-xs-12 col-lg-6' id='gas'>
			<h3 class='dash-title'>Average Carbon Monoxide measurements</h3>
			<canvas id='gas-chart'></canvas>
			<div id='gas-data'></div>
		</div>
		<div class='col-xs-12 col-lg-6' id='solar'>
			<h3 class='dash-title'>Solar Energy Generated</h3>
			<canvas id='solar-chart'></canvas>
			<div id='solar-data'></div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		var lightchart = null;
		var moisturechart = null;
		var tempchart = null;
		var humidchart = null;
		var gaschart = null;
		var solarchart = null;

		window.charts = [lightchart, moisturechart, tempchart, humidchart, gaschart, solarchart];
		resetSiteDevices();

		// Button handler for generating graphs on this page
		$('#gph-button').click(function() {
			resetSiteDevices();
			$.each(window.charts, function(index, chart) {
				if (chart) chart.destroy();
			});

			// Aggregate site sensors into different types 
			var site_selected = $('#gph-sites-select option:selected').val();
			// Check if the site is outside
			if (site_selected.includes("|")) {
				var zone_selected = site_selected.substr(site_selected.indexOf("|") + 1);
				$.each(window.sites['outside']['zones'][zone_selected]['devices'], function(deviceIndex, device) {
					window.siteDevices[device['type']].push(device);
				});
			} else {
				$.each(window.sites[site_selected]['zones'], function(zoneIndex, zone) {
					$.each(zone['devices'], function(deviceIndex, device) {
						window.siteDevices[device['type']].push(device);
					});
				});
			}

			// Fill the appropriate graphs and turn off the rest
			// Lumosity
			if (window.siteDevices['lumosity'].length == 0) $('#light').hide();
			else {
				$('#light').show();
				var num_devices = window.siteDevices['lumosity'].length;
				var length = window.siteDevices['lumosity'][0]['readings'].length;
				var timestamps = window.siteDevices['lumosity'][0].timestamps;
				$.each(window.siteDevices['lumosity'], function(index, device) {
					if (device.readings.length < length) length = device.readings.length;
				});
				// Build our average data array
				var avg_readings = [];
				for (i = 0; i < length; i++) {
					var sum = 0;
					$.each(window.siteDevices['lumosity'], function(index, device) {
						sum += device.readings[i];
					});
					avg_readings.push(sum/num_devices)
				}
				var movingavg_readings = movingAverage(avg_readings, length);

				$('#light-chart').html('');
				var ctx = document.getElementById('light-chart').getContext('2d');
				lightchart = getChart(ctx, movingavg_readings, timestamps);
				var max = maxData(avg_readings);
				var min = minData(avg_readings);
				$('#light-data').html('Maximum: ' + max + ", Minimum: " + min);
			}

			// hydrometer
			if (window.siteDevices['hydrometer'].length == 0) $('#moisture').hide();
			else {
				$('#moisture').show();
				var num_devices = window.siteDevices['hydrometer'].length;
				var length = window.siteDevices['hydrometer'][0]['readings'].length;
				var timestamps = window.siteDevices['hydrometer'][0].timestamps;
				$.each(window.siteDevices['hydrometer'], function(index, device) {
					if (device.readings.length < length) length = device.readings.length;
				});
				// Build our average data array
				var avg_readings = [];
				for (i = 0; i < length; i++) {
					var sum = 0;
					$.each(window.siteDevices['hydrometer'], function(index, device) {
						sum += device.readings[i];
					});
					avg_readings.push(sum/num_devices)
				}
				var movingavg_readings = movingAverage(avg_readings, length);

				$('#moisture-chart').html('');
				var ctx = document.getElementById('moisture-chart').getContext('2d');
				moisturechart = getChart(ctx, movingavg_readings, timestamps)
				var max = maxData(avg_readings);
				var min = minData(avg_readings);
				$('#moisture-data').html('Maximum: ' + max + ", Minimum: " + min);
			}

			// gas
			if (window.siteDevices['gas'].length == 0) $('#gas').hide();
			else {
				$('#gas').show();
				var num_devices = window.siteDevices['gas'].length;
				var length = window.siteDevices['gas'][0]['readings'].length;
				var timestamps = window.siteDevices['gas'][0].timestamps;
				$.each(window.siteDevices['gas'], function(index, device) {
					if (device.readings.length < length) length = device.readings.length;
				});
				// Build our average data array
				var avg_readings = [];
				for (i = 0; i < length; i++) {
					var sum = 0;
					$.each(window.siteDevices['gas'], function(index, device) {
						sum += device.readings[i];
					});
					avg_readings.push(sum/num_devices)
				}
				var movingavg_readings = movingAverage(avg_readings, length);

				$('#gas-chart').html('');
				var ctx = document.getElementById('gas-chart').getContext('2d');
				gaschart = getChart(ctx, movingavg_readings, timestamps);
				var max = maxData(avg_readings);
				var min = minData(avg_readings);
				$('#gas-data').html('Maximum: ' + max + ", Minimum: " + min);
			}

			// solar
			if (window.siteDevices['solar'].length == 0) $('#solar').hide();
			else {
				$('#solar').show();
				var num_devices = window.siteDevices['solar'].length;
				var length = window.siteDevices['solar'][0]['readings'].length;
				var timestamps = window.siteDevices['solar'][0].timestamps;
				$.each(window.siteDevices['solar'], function(index, device) {
					if (device.readings.length < length) length = device.readings.length;
				});
				// Build our average data array
				var avg_readings = [];
				for (i = 0; i < length; i++) {
					var sum = 0;
					$.each(window.siteDevices['solar'], function(index, device) {
						sum += device.readings[i];
					});
					avg_readings.push(sum/num_devices)
				}
				var movingavg_readings = movingAverage(avg_readings, length);

				$('#solar-chart').html('');
				var ctx = document.getElementById('solar-chart').getContext('2d');
				solarchart = getChart(ctx, movingavg_readings, timestamps);
				var max = maxData(avg_readings);
				var min = minData(avg_readings);
				$('#solar-data').html('Maximum: ' + max + ", Minimum: " + min);
			}

			// tempHumid
			if (window.siteDevices['tempHumid'].length == 0) {
				$('#temperature').hide();
				$('#humidity').hide();
			}
			else {
				$('#temperature').show();
				$('#humidity').show();
				var num_devices = window.siteDevices['tempHumid'].length;
				var length = window.siteDevices['tempHumid'][0]['readings'].length;
				var secondLength = window.siteDevices['tempHumid'][0]['secondaryReadings'].length;
				var timestamps = window.siteDevices['tempHumid'][0].timestamps;
				$.each(window.siteDevices['tempHumid'], function(index, device) {
					if (device.readings.length < length) length = device.readings.length;
					if (device.secondaryReadings.length < length) secondLength = device.secondaryReadings.length;
				});
				// Build our average data array
				var avg_readings = [];
				for (i = 0; i < length; i++) {
					var sum = 0;
					$.each(window.siteDevices['tempHumid'], function(index, device) {
						sum += device.readings[i];
					});
					avg_readings.push(sum/num_devices)
				}
				var movingavg_readings = movingAverage(avg_readings, length);

				$('#temperature-chart').html('');
				var ctx = document.getElementById('temperature-chart').getContext('2d');
				tempchart = getChart(ctx, movingavg_readings, timestamps);
				var max = maxData(avg_readings);
				var min = minData(avg_readings);
				$('#temperature-data').html('Maximum: ' + max + ", Minimum: " + min);

				// Do the same for humidity
				var avg_readings = [];
				for (i = 0; i < secondLength; i++) {
					var sum = 0;
					$.each(window.siteDevices['tempHumid'], function(index, device) {
						sum += device.secondaryReadings[i];
					});
					avg_readings.push(sum/num_devices)
				}
				var movingavg_readings = movingAverage(avg_readings, length);

				$('#humidity-chart').html('');
				var ctx = document.getElementById('humidity-chart').getContext('2d');
				humidchart = getChart(ctx, movingavg_readings, timestamps);
				var max = maxData(avg_readings);
				var min = minData(avg_readings);
				$('#humidity-data').html('Maximum: ' + max + ", Minimum: " + min);
			}
		});
	
	$('#gph-button').trigger('click');

	function resetSiteDevices() {
		window.siteDevices = {
			'lumosity': [],
			'hydrometer': [],
			'tempHumid': [],
			'gas': [],
			'solar': [],
		}
	}

	// Creates a new Chart.JS chart
	function getChart(context, data, labels) {
		return new Chart(context, {
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
				},
				// animation: false,
			}
		});
	}

	function maxData($data) {
		return $data.reduce(function(a, b) {
			return Math.max(a, b);
		});
	}

	function minData($data) {
		return $data.reduce(function(a, b) {
			return Math.min(a, b);
		});
	}

	// Performs moving average smoothing on device data.
	function movingAverage(avg_readings, length) {
		var movingavg_readings = [];
		movingavg_readings.push(avg_readings[0]);
		for (i = 1; i < length - 1; i++) {
			var mean = (avg_readings[i-1] + avg_readings[i] + avg_readings[i+1])/3.0
			movingavg_readings.push(mean);
		}
		movingavg_readings.push(avg_readings[avg_readings.length - 1]);
		return movingavg_readings;
	}

</script>
@endsection
