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
			window.sitedevices = 
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
</script>
@endsection