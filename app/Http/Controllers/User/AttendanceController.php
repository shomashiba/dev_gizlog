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
                                    //    dd($attendance);
        $status = $this->confirmAttendance($attendance);
        return view('user.attendance.index', compact('attendance', 'status'));
    }

    public function confirmAttendance($data)
    {
        if (!empty($data->is_absent)) {
            return 'absent';
        }

        if (empty($data->start_time) && empty($data->end_time)) {
            return 'not_attend';
        }

        if (!empty($data->start_time) && empty($data->end_time)) {
            return 'start';
        }

        if (!empty($data->start_time) && !empty($data->end_time)) {
            return 'end';
        }
    }

    public function registerStartTime(Request $request) 
    {
        $attendance = $request->all();
        $attendance['user_id'] = Auth::id();
        $this->attendance->create($attendance);
        return redirect()->route('attendance.index');
    }

    public function registerEndTime(Request $request, $id) 
    {
        $attendance = $request->all();
        $this->attendance->find($id)->update($attendance);
        return redirect()->route('attendance.index');
    }


}
