<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\AttendanceRequest;
use App\Http\Requests\User\RegisterTimeRequest;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

const CONVERT_HOURS = 60;

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
     * @param Illuminate\Support\Collection $data
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
    public function registerStartTime(RegisterTimeRequest $request) 
    {
        $attendance = $request->validated();
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
    public function registerEndTime(RegisterTimeRequest $request, $id) 
    {
        $attendance = $request->validated();
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
    public function registerAbsence(AttendanceRequest $request)
    {
        $absence = $request->validated();
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
    public function registerModify(AttendanceRequest $request)
    {
        $modify = $request->validated();
        $modify['is_request'] = true;
        $this->attendance->update(
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
            ->orderBy('date','desc')
            ->get();
        $totalStudyTime = $this->calcStudyTime($attendances);
        return view('user.attendance.mypage', compact('attendances', 'totalStudyTime'));
    }

    /**
     * 出社日の合計学習時間の産出
     *
     * @param Illuminate\Support\Collection $datas
     * @return int $totalStudyTime
     */
    public function calcStudyTime($datas)
    {
        $totalStudyHours = 0;
        $datas = $datas->whereNotIn('end_time', '')->all();

        foreach ($datas as $data) {
            $studyMinutes = $data->start_time->diffInMinutes($data->end_time);
            $totalStudyHours += round($studyMinutes / CONVERT_HOURS);
        }
        return $totalStudyHours;
    }
}
