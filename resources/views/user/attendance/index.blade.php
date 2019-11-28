@extends ('common.user')
@section ('content')

<h2 class="brand-header">勤怠登録</h2>

<div class="main-wrap">

  <div id="clock" class="light">
    <div class="display">
      <div class="weekdays"></div>
      <div class="today"></div>
      <div class="digits"></div>
    </div>
  </div>
  <div class="button-holder">
    @if ($status === 'not_attend')
      <a class="button start-btn" id="register-attendance" href=#openModal>出社時間登録</a>
    @elseif ($status === 'attend')
      <a class="button end-btn" id="register-attendance" href=#openModal>退社時間登録</a>
    @elseif ($status === 'leave')
      <a class="button disabled" id="register-attendance" href=#openModal>退社済み</a>
    @elseif ($status === 'absent')
      <a class="button disabled" id="register-attendance" href=#openModal>欠席</a>
    @endif
  </div>
  <ul class="button-wrap">
    <li>
      <a class="at-btn absence" href="/attendance/absence">欠席登録</a>
    </li>
    <li>
      <a class="at-btn modify" href="/attendance/modify">修正申請</a>
    </li>
    <li>
      <a class="at-btn my-list" href="/attendance/mypage">マイページ</a>
    </li>
  </ul>
</div>

<div id="openModal" class="modalDialog">
  <div>
    <div class="register-text-wrap"><p>{{ Carbon::now()->format('H:i') }} で{{ ($status === 'not_attend') ? '出社' : '退社' }}時間を登録しますか？</p></div>
    <div class="register-btn-wrap">
      @if ($status === 'not_attend')
        {!! Form::open(['route' => 'attendance.registerStart']) !!}
        {!! Form::hidden('start_time', Carbon::now(), ['id' => 'date-time-target']) !!}
      @elseif ($status === 'attend')
        {!! Form::open(['route' => ['attendance.registerEnd', $attendance->id], 'method' => 'put']) !!}
        {!! Form::hidden('end_time', Carbon::now(), ['id' => 'date-time-target']) !!}
      @endif
      {!! Form::hidden('date', Carbon::now()->format('Y-m-d')) !!}
      <a href="#close" class="cancel-btn">Cancel</a>
      {!! Form::submit('Yes', ['class' => 'yes-btn']) !!}
      {!! Form::close() !!}
    </div>
  </div>
</div>

@endsection

