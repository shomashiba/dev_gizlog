<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\AttendanceRequest;
use App\Http\Requests\User\RegisterTimeRequest;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

const DATETIME = 'Y-m-d';

class AttendanceController extends Controller
{

    private $attendance;
    private $service;

    /**
     * ユーザーの認証状態チェック
     * Modelのインジェクション
     *
     * @param Attendance $attendance
     */
    public function __construct(Attendance $attendance, 
                                AttendanceService $service)
    {
        $this->middleware('auth');
        $this->attendance = $attendance;
        $this->service = $service;
    }

    /**
     * 勤怠登録画面。
     * ユーザーの勤怠情報を確認。
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $attendance = $this->attendance->fetchAttendance(
            Auth::id(), Carbon::today()->format(DATETIME)
        );
        $status = $this->service->confirmAttendanceState($attendance);
        return view('user.attendance.index', compact('attendance', 'status'));
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
                'date' => Carbon::today()->format(DATETIME), 
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
        $this->attendance->feachAttendance(Auth::id(), $modify['date'])
                         ->update($modify);
        return redirect()->route('attendance.index');
    }

    /**
     * マイページ画面
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showMypage()
    {
        $attendances = $this->attendance->fetchMyAttendance(Auth::id());
        $totalStudyTime = $this->service->calcStudyTime($attendances);
        return view('user.attendance.mypage', compact('attendances', 'totalStudyTime'));
    }

}
