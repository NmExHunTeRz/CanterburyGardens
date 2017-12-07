<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>Canterbury Gardens</title>

	<!-- Styles -->
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/bootstrap-theme.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="https://bootswatch.com/3/cosmo/bootstrap.css">
	<link href="{{ asset('css/main.css') }}" rel="stylesheet">

	<!-- JS -->
	<script src="https://use.fontawesome.com/0363e80ff7.js"></script>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

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

</head>
<body>
	<div id="app">
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="#">Canterbury Gardens</a>
				</div>
				<div class="collapse navbar-collapse" id="app-navbar-collapse">
					{{--Left Side Of Navbar--}}
					{{--<ul class="nav navbar-nav">--}}
						{{--<li><a href="./locations/gh1.html">Greenhouse 1</a></li>--}}
						{{--<li><a href="./locations/gh2.html">Greenhouse 2</a></li>--}}
						{{--<li><a href="./locations/gh3.html">Greenhouse 3</a></li>--}}
						{{--<li><a href="./locations/outdoors.html">Outdoor Plots</a></li>--}}
						{{--<li><a href="./locations/muck.html">Muck Heap</a></li>--}}
						{{--<li><a href="./locations/store.html">Store Room</a></li>--}}
						{{--<li><a href="./locations/solar.html">Solar Plant</a></li>--}}
					{{--</ul>--}}
					{{--Right Side Of Navbar--}}
					<ul class="nav navbar-nav navbar-right">
						@guest
							<li><a href="">Login</a></li>
							<li><a href="">Register</a></li>
							{{--<li><a href="{{ route('login') }}">Login</a></li>--}}
							{{--<li><a href="{{ route('register') }}">Register</a></li>--}}
						@else
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
									{{ Auth::user()->name }} <span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li>
										<a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
											Logout
										</a>
										<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
											{{ csrf_field() }}
										</form>
									</li>
								</ul>
							</li>
						@endguest
					</ul>
				</div>
			</div>
		</nav>
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-12 main">
					@yield('content')
				</div>
			</div>
		</div>
	</div>

	<!-- Footer -->
	<footer class="footer">
		<div class="container">
			<p class="text-muted">Cooksey Farms</p>
		</div>
	</footer>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
	<script src="{{ asset('js/app.js') }}"></script>
	<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAldSd8XQP3VPXbcOQ4smOrpH-rBy3r7O8&callback=initMap"></script>
</body>
</html>