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
        $attendance = $this->attendance->where('date', Carbon::now()->format('Y-m-d'))
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

    public function showAbsence()
    {
        return view('user.attendance.absence');
    }

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
}
