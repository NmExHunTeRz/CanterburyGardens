@extends('layouts.app')

@section('header')
<h1>Weather Test</h1>
@endsection

@section('content')
    <div class="container weather_data text-center">
        <div class="row">
            <div class="col-sm-4">
                <div class="well well-lg">
                    <h3>Past 24 Hours Rainfall:</h3>
                    {{$rainfall}} inches
                </div>
            </div>
            <div class="col-sm-4">
                <div class="well well-lg">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="well well-lg">
                </div>
            </div>
        </div>
    </div>
@endsection
