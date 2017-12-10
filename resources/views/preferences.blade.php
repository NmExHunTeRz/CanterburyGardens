@extends('layouts.app')
@section('title', 'Preferences')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                @if (\Illuminate\Support\Facades\Session::has('success'))
                    <p class="text-success"><i class="fa fa-check" aria-hidden="true"></i> {{ session('success') }}</p>
                @endif

            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <form method="POST" action="/preferences">
                    {{ csrf_field() }} {{-- Needed within all forms to prevent CSRF attacks --}}
                    <table class="table table-striped table-responsive table-condensed preferences_table">
                        <thead>
                        <tr>
                            <th>Site</th>
                            <th>High Humidity</th>
                            <th>Low Humidity</th>
                            <th>High Moisture</th>
                            <th>Low Moisture</th>
                            <th>High Lux</th>
                            <th>Low Lux</th>
                            <th>High Gas</th>
                            <th>Low Gas</th>
                            <th>High Temp</th>
                            <th>Low Temp</th>
                            <th>High Winter Temp</th>
                            <th>Low Winter Temp</th>
                        </tr>
                        </thead>
                        @foreach ($conditions as $condition)
                            <tr>
                                <td>{{$condition->site_id}}</td>
                                <td><input type="number" value="{{$condition->high_humidity}}" name="{{$condition->id}}[high_humidity]"></td>
                                <td><input type="number" value="{{$condition->low_humidity}}" name="{{$condition->id}}[low_humidity]"></td>
                                <td><input type="number" value="{{$condition->high_moisture}}" name="{{$condition->id}}[high_moisture]"></td>
                                <td><input type="number" value="{{$condition->low_moisture}}" name="{{$condition->id}}[low_moisture]"></td>
                                <td><input type="number" value="{{$condition->high_lux}}" name="{{$condition->id}}[high_lux]"></td>
                                <td><input type="number" value="{{$condition->low_lux}}" name="{{$condition->id}}[low_lux]"></td>
                                <td><input type="number" value="{{$condition->high_gas}}" name="{{$condition->id}}[high_gas]"></td>
                                <td><input type="number" value="{{$condition->low_gas}}" name="{{$condition->id}}[low_gas]"></td>
                                <td><input type="number" value="{{$condition->high_temp}}" name="{{$condition->id}}[high_temp]"></td>
                                <td><input type="number" value="{{$condition->low_temp}}" name="{{$condition->id}}[low_temp]"></td>
                                <td><input type="number" value="{{$condition->winter_high_temp}}" name="{{$condition->id}}[winter_high_temp]"></td>
                                <td><input type="number" value="{{$condition->winter_low_temp}}" name="{{$condition->id}}[winter_low_temp]"></td>
                            </tr>
                        @endforeach
                    </table>
                    <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-check" aria-hidden="true"></i> Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
