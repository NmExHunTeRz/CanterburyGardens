<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title') - Canterbury Gardens</title>
	<link rel="icon" type="image/png" href="favicon.png"/> {{-- Favicon credit: http://www.blocksandgold.com/en/minecraft-iron-hoe.html --}}
	<!-- Styles -->
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/bootstrap-theme.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="https://bootswatch.com/3/cosmo/bootstrap.css">
	<link href="{{ asset('css/main.css') }}" rel="stylesheet">
	<!-- JS -->
	<script src="https://use.fontawesome.com/0363e80ff7.js"></script>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

	<script>
        $(document).ready(function(){
            $('.dropdown-toggle').dropdown();
        });
	</script>
</head>
<body>
	<div id="app">
		<nav class="navbar navbar-fixed-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="/">Canterbury Gardens</a>
				</div>
				<div class="collapse navbar-collapse" id="app-navbar-collapse">
					{{--Left Side Of Navbar--}}
					<ul class="nav navbar-nav">
						<li><a href="/"><i class="fa fa-home" aria-hidden="true"></i> Dashboard</a></li>
						<li><a href="/graphs"><i class="fa fa-bar-chart" aria-hidden="true"></i> Graphs</a></li>
					</ul>
					{{--Right Side Of Navbar--}}
					<ul class="nav navbar-nav navbar-right">
						@guest
							<li><a href="{{ route('login') }}"><i class="fa fa-sign-in" aria-hidden="true"></i> Login</a></li>
						@else
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
									<i class="fa fa-user" aria-hidden="true"></i> {{ Auth::user()->name }} <span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li>
										<a href="/preferences"><i class="fa fa-cog" aria-hidden="true"></i> Preferences</a>
										<a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
											<i class="fa fa-sign-out" aria-hidden="true"></i> Logout
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
		<div class='header-container'>
			@yield('header')
		</div>
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
			<p class="text-muted">Canterbury Gardens Ltd - {{ date("Y") }}</p>
		</div>
	</footer>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
	<script src="{{ asset('js/app.js') }}"></script>
	<script async defer
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAldSd8XQP3VPXbcOQ4smOrpH-rBy3r7O8&callback=initMap">
	</script>
</body>
</html>