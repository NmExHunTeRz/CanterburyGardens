@extends('layouts.app')

@section('header')
<h1>Graphs</h1>
@endsection

@section('content')
<script type='text/javascript'>
	window.sites = {!! json_encode($sites) !!};
	console.log(sites);
</script>
<div class='container graphs-container'>
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
		</div>
		<div class='col-xs-12 col-lg-6' id='moisture'>
			<h3 class='dash-title'>Average Moisture Measurements</h3>
			<canvas id='moisture-chart'></canvas>
		</div>
		<div class='col-xs-12 col-lg-6' id='temperature'>
			<h3 class='dash-title'>Average Temperature Measurements</h3>
			<canvas id='temperature-chart'></canvas>
		</div>
		<div class='col-xs-12 col-lg-6' id='humidity'>
			<h3 class='dash-title'>Average Humidity Measurements</h3>
			<canvas id='humidity-chart'></canvas>
		</div>
		<div class='col-xs-12 col-lg-6' id='gas'>
			<h3 class='dash-title'>Average Carbon Monoxide measurements</h3>
			<canvas id='gas-chart'></canvas>
		</div>
		<div class='col-xs-12 col-lg-6' id='solar'>
			<h3 class='dash-title'>Solar Energy Generated</h3>
			<canvas id='solar-chart'></canvas>
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
				// Determine minimum length in data arrays, in case they are different
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

				$('#light-chart').html('');
				var ctx = document.getElementById('light-chart').getContext('2d');
				lightchart = getChart(ctx, avg_readings, timestamps);
			}

			// hydrometer
			if (window.siteDevices['hydrometer'].length == 0) $('#moisture').hide();
			else {
				$('#moisture').show();
				var num_devices = window.siteDevices['hydrometer'].length;
				// Determine minimum length in data arrays, in case they are different
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

				$('#moisture-chart').html('');
				var ctx = document.getElementById('moisture-chart').getContext('2d');
				lightchart = getChart(ctx, avg_readings, timestamps);
			}

			// gas
			if (window.siteDevices['gas'].length == 0) $('#gas').hide();
			else {
				$('#gas').show();
				var num_devices = window.siteDevices['gas'].length;
				// Determine minimum length in data arrays, in case they are different
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

				$('#gas-chart').html('');
				var ctx = document.getElementById('gas-chart').getContext('2d');
				lightchart = getChart(ctx, avg_readings, timestamps);
			}

			// solar
			if (window.siteDevices['solar'].length == 0) $('#solar').hide();
			else {
				$('#solar').show();
				var num_devices = window.siteDevices['solar'].length;
				// Determine minimum length in data arrays, in case they are different
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

				$('#solar-chart').html('');
				var ctx = document.getElementById('solar-chart').getContext('2d');
				lightchart = getChart(ctx, avg_readings, timestamps);
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
				// Determine minimum length in data arrays, in case they are different
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

				$('#temperature-chart').html('');
				var ctx = document.getElementById('temperature-chart').getContext('2d');
				lightchart = getChart(ctx, avg_readings, timestamps);

				// Do the same for humidity
				var avg_readings = [];
				for (i = 0; i < secondLength; i++) {
					var sum = 0;
					$.each(window.siteDevices['tempHumid'], function(index, device) {
						sum += device.secondaryReadings[i];
					});
					avg_readings.push(sum/num_devices)
				}

				$('#humidity-chart').html('');
				var ctx = document.getElementById('humidity-chart').getContext('2d');
				lightchart = getChart(ctx, avg_readings, timestamps);
			}

		});
	});

	function resetSiteDevices() {
		window.siteDevices = {
			'lumosity': [],
			'hydrometer': [],
			'tempHumid': [],
			'gas': [],
			'solar': [],
		}
	}

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
				}
			}
		});
	}

</script>
@endsection
