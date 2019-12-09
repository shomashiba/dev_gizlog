@extends ('common.user')
@section ('content')

<h2 class="brand-header">マイページ</h2>

<div class="main-wrap">
  <div class="btn-wrapper">
    <div class="my-info day-info">
      <p>学習経過日数</p>
      <div class="study-hour-box clearfix">
        <div class="userinfo-box"><img src="{{ Auth::user()->avatar }}"></div>
        <p class="study-hour"><span>{{ $attendances->where('is_absent', false)->whereNotIn('end_time', '')->count() }}</span>日</p>
      </div>
    </div>
    <div class="my-info">
      <p>累計学習時間</p>
      <div class="study-hour-box clearfix">
        <div class="userinfo-box"><img src="{{ Auth::user()->avatar }}"></div>
        <p class="study-hour"><span>{{ $totalStudyTime }}</span>時間</p>
      </div>
    </div>
  </div>
  <div class="content-wrapper table-responsive">
    <table class="table">
      <thead>
        <tr class="row">
          <th class="col-xs-2">date</th>
          <th class="col-xs-3">start time</th>
          <th class="col-xs-3">end time</th>
          <th class="col-xs-2">state</th>
          <th class="col-xs-2">request</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($attendances as $attendance)
          <tr class="row @if (isset($attendance->is_absent) && $attendance->is_absent === true) {{ 'absent-row' }} @endif">
            <td class="col-xs-2">{{ $attendance->date ? $attendance->date->format('m/d (D)') : '-' }}</td>
            <td class="col-xs-3">{{ $attendance->start_time ? $attendance->start_time->format('H:i') : '-' }}</td>
            <td class="col-xs-3">{{ $attendance->end_time ? $attendance->end_time->format('H:i') : '-' }}</td>
            <td class="col-xs-2">
              @if ($attendance->is_absent === true)
                {{ '欠席' }}
              @elseif (isset($attendance->start_time, $attendance->end_time))
                {{ '出社' }}
              @elseif (isset($attendance->start_time))
                {{ '研修中' }}
              @endif
            </td>
            <td class="col-xs-2">{{ (isset($attendance->is_requesting) && $attendance->is_requesting) ? '申請中' : '-' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection

