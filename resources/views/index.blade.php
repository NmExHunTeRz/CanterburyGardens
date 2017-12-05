@extends('layouts.app')

@section('content')
    <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="./main.html">Cooksey Farms</a>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-3 col-md-2 sidebar">
        <ul class="nav nav-sidebar">
          <li class="active">
            <a href="./main.html">Dashboard</a>
          </li>
          <li>
            <a href="https://www.metoffice.gov.uk/public/weather/forecast/u10g8x4vg">Weather</a>
          </li>

        </ul>
        <ul class="nav nav-sidebar">
          <li>
            <a href="./locations/gh1.html">Greenhouse 1</a>
          </li>
          <li>
            <a href="./locations/gh2.html">Greenhouse 2</a>
          </li>
          <li>
            <a href="./locations/gh3.html">Greenhouse 3</a>
          </li>
          <li>
            <a href="./locations/outdoors.html">Outdoor Plots</a>
          </li>
          <li>
            <a href="./locations/muck.html">Muck Heap</a>
          </li>
          <li>
            <a href="./locations/store.html">Store Room</a>
          </li>
        </ul>
        <ul class="nav nav-sidebar">
          <li>
            <a href="./locations/solar.html">Solar Plant</a>
          </li>
        </ul>
      </div>
      <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h1 class="page-header">Dashboard</h1>
        <div class="jumbotron">
          <h3>Daily Notices for
            <span id="date"></span>
          </h3>
          <p>There are no additional tasks for today!</p>
        </div>
        <div class="row">
          <div class="col-sm-8 map">
            Huy's map will go here
          </div>
          <div class="col-sm-4 weather">
            The weather widget will go here
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="footer">
    <div class="container">
      <p class="text-muted">Cooksey Farms</p>
    </div>
  </footer>
@endsection