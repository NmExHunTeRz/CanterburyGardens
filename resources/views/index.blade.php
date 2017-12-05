@extends('layouts.app')

@section('content')
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
            <div id="openweathermap-widget-15"></div>
            <script>window.myWidgetParam ? window.myWidgetParam : window.myWidgetParam = []; window.myWidgetParam.push({ id: 15, cityid: '2653877', appid: '207c0e71469a721950916a9720d0ae71', units: 'metric', containerid: 'openweathermap-widget-15', }); (function () { var script = document.createElement('script'); script.async = true; script.charset = "utf-8"; script.src = "https://openweathermap.org/themes/openweathermap/assets/vendor/owm/js/weather-widget-generator.js"; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(script, s); })();</script>
          </div>
        </div>
@endsection