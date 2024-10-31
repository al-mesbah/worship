<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function get()
    {
        return Reservation::query()->where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
    }

    public function reserved(Request $request)
    {
        $user = Auth::user();
        if ($user->pn_field14 == null || $user->pn_field14 == 0 || $user->pn_field14 == '') {
            return [
                'status'  => false,
                'message' => 'شما کد عبادت ندارید!'
            ];
        }
        if ($user->pn_break != 0 || (Carbon::parse($user->pn_breakdate)->isPast() && $user->pn_breakdate != '0000-00-00 00:00:00')) {
            return [
                'status'  => false,
                'message' => 'شما دارای شهریه فعال نمی باشید.'
            ];
        }
        $last = $user->pn_field117;
        $carbon = Carbon::parse($last);
        $targetDate = Carbon::now()->subDays(Setting::query()->find(168)->pn_value);
        function day_append($total = 30)
        {
            if ($total == 29) {
                $total = 30;
            }
            $r = '';
            if ($total / 30 >= 1) {
                switch ((int)floor($total / 30)) {
                    case 1:
                        $r = 'یک';
                        break;
                    case 2:
                        $r = 'دو';
                        break;
                    case 3:
                        $r = 'سه';
                        break;
                    case 4:
                        $r = 'چهار';
                        break;
                    case 5:
                        $r = 'پنج';
                        break;
                    case 6:
                        $r = 'شش';
                        break;
                    case 7:
                        $r = 'هفت';
                        break;
                }
                $r .= ' ماه ';
            }
            $days = $total - (int)floor($total / 30) * 30;
            $prefix = '';
            if ($days) {
                if ($r != '') {
                    $prefix = ' و ';
                }
                $prefix .= $days . ' روز ';
            }
            return $r . $prefix;
        }

        if ($last === null || $carbon->isBefore($targetDate)) {
            if (Reservation::query()->where('user_id', Auth::id())->where('is_active', false)->exists()) {
                return [
                    'status'  => false,
                    'message' => 'شما یک نوبت معوقه دارید!'
                ];
            }
            Auth::user()->update([
                'pn_field117' => date('Y-m-d H:i:s')
            ]);
            Reservation::query()->create([
                'user_id'     => Auth::id(),
                'reserved_at' => date('Y-m-d H:i:s')
            ]);
            return [
                'status'  => true,
                'message' => 'نوبت شما با موفقیت ثبت گردید، جهت دریافت روزه منتظر پیام ما باشید.'
            ];
        }
        return [
            'status'  => false,
            'message' => 'آخرین نوبت شما مربوط به کمتر از ' . day_append(Setting::query()->find(168)->pn_value) . ' اخیر می باشد بعدا تلاش کنید!'
        ];

    }
}
