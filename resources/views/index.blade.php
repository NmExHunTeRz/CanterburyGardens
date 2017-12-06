@extends('layouts.app')

@section('content')
	<script type='text/javascript'>
		window.sites = {!! json_encode($sites) !!};
		console.log(sites);
		window.sites = {!! json_encode($devices) !!};
		console.log(devices);

		$.foreach(window.sites, function(siteIndex, site) {

		});
	</script>


	<h1 class="page-header">Dashboard</h1>
	<div class='container dash-container'>
		<div class='row'>
			<div class='col-xs-12 col-md-6 col-lg-6 dash-notices'>
				<h3>Notifications for <span id="date"></span></h3>
				<ul id='notifications'></ul>
			</div>
			<div class='col-xs-12 col-md-6 col-lg-6 dash-status'>
				<h3>Device statuses</h3>
				<div id='statuses'>
					<ul>

					</ul>
				</div>
			</div>
		</div>
	</div>
@endsection