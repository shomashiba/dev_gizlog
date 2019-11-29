<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\AttendanceRequest;
use App\Http\Requests\User\RegisterTimeRequest;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;

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
    public function __construct(Attendance $attendance, AttendanceService $service)
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
        $attendance = $this->service->fetchAttendance(Auth::id(), $this->service->today);
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
        $inputs = $request->validated();
        $inputs['user_id'] = Auth::id();
        $this->attendance->create($inputs);
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
        $this->attendance->find($id)->update(['end_time' => $attendance['end_time']]);
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
        $inputs = $request->validated();
        $inputs['user_id'] = Auth::id();
        $this->service->storeAbsence($inputs, $this->service->today);
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
        $inputs = $request->validated();
        $inputs['user_id'] = Auth::id();
        $this->service->storeModify($inputs, $this->service->today);
        return redirect()->route('attendance.index');
    }

    /**
     * マイページ画面
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showMypage()
    {
        $attendances = $this->service->fetchMyAttendance(Auth::id());
        $totalStudyTime = $this->calcStudyTime($attendances);
        return view('user.attendance.mypage', compact('attendances', 'totalStudyTime'));
    }

}
