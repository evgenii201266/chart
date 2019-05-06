@extends('layouts.app')

@section('content')


<div class="col-md-2">
  <div class="input-group date">
  {!! Form::open(['route' => ['parser.index']]) !!}
      {{Form::text('catalogId', null, ['class' => 'form-control'])}}

      @if ($errors->has('catalogId'))
      <div class="alert alert-danger">
        <strong>{{ $errors->first('catalogId')}}</strong>
      </div>
      @endif

      <button type="submit" style="width=100px"class="btn btn-primary btn-flat">Получить</button>
  {!! Form::close() !!}
  </div>
</div>


@endsection
