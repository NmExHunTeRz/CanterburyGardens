@extends('layouts.app')

@section('content')
    <h1 class="page-header">Dashboard</h1>
        <div class="jumbotron">
          <h3>Daily Notices for
            <span id="date"></span>
          </h3>
          <p><script>
              console.log({!! json_encode($sites) !!});
          </script></p>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @foreach ($devices as $device)
                    <div class="col-sm-2">
                        <div class="panel panel-default" style="text-align: center">
                            <div class="panel-body">
                                <p>{{$device->name}}</p>
                                <i class="fa fa-check text-success fa-2x" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

          <div class="col-sm-8 map">
            Huy's map will go here
          </div>
          <div class="col-sm-4 weather">
          </div>
        </div>
@endsection