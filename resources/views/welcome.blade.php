@extends('layouts.app')

@section('content')
  <script src="{{ asset('js/core.js') }}"></script>
  <script src="{{ asset('js/charts.js') }}"></script>
  <script src="{{ asset('js/dark.js') }}"></script>
  <script src="{{ asset('js/jquery.min.js') }}"></script>
  <script src="{{ asset('js/moment.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>

  <div class="col-md-2">
  <div class="input-group date">
      {{Form::text('catalogId', null, ['class' => 'form-control', 'id' => 'contract_datetime'])}}
      @if ($errors->has('catalogId'))
      <div class="alert alert-danger">
        <strong>{{ $errors->first('catalogId')}}</strong>
      </div>
      @endif

      <button type="submit" id ='click' class="btn btn-primary btn-flat">Получить</button>
  </div>
  </div>

  <div id="chartdiv"></div>

  <script src="{{ asset('js/mychart.js') }}"></script>

  <script>
      $('#contract_datetime').datetimepicker({
        viewMode: 'years',
        format: 'YYYY-MM'
      });
  </script>

  @endsection