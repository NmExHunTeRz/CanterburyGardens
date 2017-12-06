@extends('layouts.app')

@section('content')
	<script type='text/javascript'>
		window.sites = {!! json_encode($sites) !!};
		console.log(sites);
		window.devices = {!! json_encode($devices) !!};
		console.log(devices);

		$.each(window.sites, function(siteIndex, site) {

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
					@foreach ($sites as $site)
						<button type='button' class='btn btn-info' data-toggle='collapse' data-target='#{{$site['id']}}-collapsible'>{{$site['name']}}</button>
						<div id='{{$site['id']}}-collapsible' class='collapse'>
							@foreach ($site['zones'] as $key=>$zone)
								<ul>{{$key}}
									@foreach ($zone['devices'] as $device)
										<li>{{$device->name}}:
											@if ($device->notify == false)
												no
											@else 
												yes
											@endif
										</li>
									@endforeach
								</ul>
							@endforeach
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
	<div class="container">
  <h2>Simple Collapsible</h2>
  <p>Click on the button to toggle between showing and hiding content.</p>
  <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo">Simple collapsible</button>
  <div id="demo" class="collapse">
    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
  </div>
</div>
@endsection