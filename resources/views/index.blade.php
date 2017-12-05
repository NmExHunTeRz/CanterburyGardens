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
          <div class="col-sm-8 map">
            Huy's map will go here
          </div>
          <div class="col-sm-4 weather">
          </div>
        </div>
@endsection