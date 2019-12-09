@extends ('common.user')
@section ('content')

<h2 class="brand-header">欠席登録</h2>
<div class="main-wrap">
  <div class="container">
    {!! Form::open(['route' => 'attendance.registerAbsence']) !!}
      <div class="form-group @if ($errors->has('absent_reason')) has-error @endif">
        {!! Form::textarea('absent_reason', null, ['class' => 'form-control', 'placeholder' => '欠席理由を入力してください。']) !!}
        <span class="help-block">{{ $errors->first('absent_reason') }}</span>
      </div>
      {!! Form::submit('登録', ['type' => 'submit', 'class' => 'btn btn-success pull-right']) !!}
    {!! Form::close() !!}
  </div>
</div>

@endsection

