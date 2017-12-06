@extends('layouts.app')

@section('content')
	<script type='text/javascript'>
		window.sites = {!! json_encode($sites) !!};
		console.log(sites);
		window.devices = {!! json_encode($devices) !!};
		console.log(devices);
	</script>

	<div class='container dash-container'>
		<div class='row'>
		</br></br>
		<h1 class="page-header">Dashboard</h1>
			<div class='col-xs-12 col-md-6 col-lg-6 dash-notices'>
				<h3>Notifications for <span id="date"></span></h3>
				<ul id='notifications'></ul>
			</div>
		</div>
		<div class="row">
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
@endsection
