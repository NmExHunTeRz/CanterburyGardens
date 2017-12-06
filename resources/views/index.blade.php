@extends('layouts.app')

@section('content')
	<script type='text/javascript'>
		window.sites = {!! json_encode($sites) !!};
		console.log(sites);
		window.devices = {!! json_encode($devices) !!};
		console.log(devices);
	</script>

	<h1 class="page-header">Dashboard</h1>
	<div class='container dash-container'>
		<div class='row'>
			<div class='col-xs-12 col-md-12 col-lg-12 dash-notices'>
				<h3>Notifications for <span id="date"></span></h3>
				<ul id='notifications'></ul>
			</div>
			<div class='col-xs-12 col-md-12 col-lg-12 dash-status'>
				<h3>Device statuses</h3>
				<div id='statuses'>
					@foreach ($sites as $site)
						<button type='button' class='btn btn-info' data-toggle='collapse' data-target='#{{$site['id']}}-collapsible'>{{$site['name']}}</button>
						<div id='{{$site['id']}}-collapsible' class='collapse'>
							<ul>
							@foreach ($site['zones'] as $key=>$zone)
								<li>{{$key}}<ul>
									@foreach ($zone['devices'] as $device)
										<li>{{$device->name}}:
											@if ($device->notify == false)
												no
											@else
												yes
											@endif
										</li>
									@endforeach
								</ul></li>
							@endforeach
							</ul>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
@endsection
