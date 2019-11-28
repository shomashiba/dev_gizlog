@extends ('common.user')
@section ('content')

<h2 class="brand-header">修正申請</h2>
<div class="main-wrap">
  <div class="container">
    {!! Form::open(['route' => 'attendance.registerModify']) !!}
      <div class="form-group form-size-small @if ($errors->has('date')) {{ 'has-error' }} @endif">
        {!! Form::input('date', 'date', null, ['class' => 'form-control']) !!}
        <span class="help-block">{{ $errors->first('date') }}</span>
      </div>
      <div class="form-group @if ($errors->has('request_reason')) {{ 'has-error' }} @endif">
        {!! Form::textarea('request_reason', null, ['class' => 'form-control', 'placeholder' => '修正申請の内容を入力してください。']) !!}
        <span class="help-block">{{ $errors->first('request_reason') }}</span>
      </div>
      {!! Form::submit('申請', ['type' => 'submit', 'class' => 'btn btn-success pull-right']) !!}
    {!! Form::close() !!}
  </div>
</div>

@endsection

