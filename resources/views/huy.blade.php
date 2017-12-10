@extends('layouts.app')

@section('header')
<h1>Weather Test</h1>
@endsection

@section('content')
    @foreach ($raindata['items'] as $data)
        {{$data['dateTime']}} :
        {{$data['value']}} </br>
    @endforeach
@endsection
