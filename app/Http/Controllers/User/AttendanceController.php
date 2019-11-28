<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{

    private $attendance;

    /**
     * ユーザーの認証状態チェック
     * Modelのインジェクション
     *
     * @param Attendance $attendance
     */
    public function __construct(Attendance $attendance)
    {
        $this->middleware('auth');
        $this->attendance = $attendance;
    }

    /**
     * 勤怠登録画面。
     * ユーザーの勤怠情報を確認。
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $attendance = $this->attendance->where('user_id', Auth::id())
            ->where('date', Carbon::now()->format('Y-m-d'))
            ->first();
        $status = $this->confirmAttendance($attendance);
        return view('user.attendance.index', compact('attendance', 'status'));
    }

    /**
     * ユーザーの出退勤状態の判別
     *
     * @param array $data
     * @return string
     */
    public function confirmAttendance($data)
    {
        if (!empty($data->is_absent)) {
            return 'absent';
        }

        if (empty($data->start_time) && empty($data->end_time)) {
            return 'not_attend';
        }

        if (!empty($data->start_time) && empty($data->end_time)) {
            return 'attend';
        }

        if (!empty($data->start_time) && !empty($data->end_time)) {
            return 'leave';
        }
    }

    /**
     * 出勤時間登録
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registerStartTime(Request $request) 
    {
        $attendance = $request->all();
        $attendance['user_id'] = Auth::id();
        $this->attendance->create($attendance);
        return redirect()->route('attendance.index');
    }

    /**
     * 退勤時間登録
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registerEndTime(Request $request, $id) 
    {
        $attendance = $request->all();
        $this->attendance->find($id)->update($attendance);
        return redirect()->route('attendance.index');
    }

    /**
     * 欠席登録画面
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showAbsence()
    {
        return view('user.attendance.absence');
    }

    /**
     * 欠席登録
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registerAbsence(Request $request)
    {
        $absence['absent_reason'] = $request->input('absent_reason');
        $absence['is_absent'] = true;
        $this->attendance->updateOrCreate(
            [
                'date' => Carbon::now()->format('Y-m-d'), 
                'user_id' => Auth::id()
            ],
            $absence
        );
        return redirect()->route('attendance.index');
    }

    /**
     * 修正申請画面
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showModify()
    {
        return view('user.attendance.modify');
    }

    /**
     * 修正申請登録
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registerModify(Request $request)
    {
        $modify = $request->all();
        $modify['is_request'] = true;
        $this->attendance->updateOrCreate(
            [
                'date' => $modify['date'], 
                'user_id' => Auth::id()
            ],
            $modify
        );
        return redirect()->route('attendance.index');
    }

    /**
     * マイページ画面
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showMypage()
    {
        $attendances = $this->attendance->where('user_id', Auth::id())
            ->get();
        $totalStudyTime = $this->calcStudyTime($attendances);
        return view('user.attendance.mypage', compact('attendances', 'totalStudyTime'));
    }

    public function calcStudyTime($datas)
    {
        $datas = $datas->whereNotIn('end_time', '')->all();
        $totalStudyTime = 0;

        foreach ($datas as $data) {
            $startTime = $data->start_time;
            $endTime = $data->end_time;
            $studyTime = $startTime->diffInHours($endTime);
            $totalStudyTime += $studyTime;
        }

        return $totalStudyTime;
    }
}
