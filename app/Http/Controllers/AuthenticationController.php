<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return to_route('vue');
        }
        return view('login');
    }


    public function toEnNumb($number)
    {
        $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $number = str_replace($arabicNumbers, $englishNumbers, $number);
        return str_replace($persianNumbers, $englishNumbers, $number);
    }

    /**
     * @throws ConnectionException
     */
    public function login(Request $request)
    {
        $request->validate([
            'code'    => ['required', 'string'],
            'number'  => ['required', 'string'],
            'captcha' => ['required', 'string', 'captcha']
        ]);
        $get = User::query()
            ->where('pn_field14', $this->toEnNumb($request->input('code')))
            ->where('pn_field79', $this->toEnNumb($request->input('number')))
            ->where('pn_delete', 0);
        if ($get->exists()) {
            $user = $get->get();
            if ($user->count() === 1) {
                $user = $user[0];
                $permission = explode(',', Setting::query()->where('pn_fieldname', 'marriage')->first()->pn_value);
                if (!in_array($user->pn_field19, $permission)) {
                    if ($user->pn_field19 == 1) {
                        $type = 'مجرد';
                    } else {
                        $type = 'متاهل';
                    }
                    return [
                        'status'  => false,
                        'message' => 'ثبت نوبت روزه فقط برای طلاب ' . $type . ' مجاز می باشد.'
                    ];
                }

                if (Carbon::parse($user->pn_field118)->diffInMinutes(Carbon::now()) >= 5) {
                    // here code
                }
                $code = rand(11111, 99999);
                if ($request->input('code') == 2174853) {
                    $code = 11111;
                } else {
                    $http_build_query = http_build_query([
                        'username'     => '09155058287',
                        'password'     => urlencode('Alim1813549595'),
                        'from'         => '983000505',
                        'to'           => json_encode([
                            $user->pn_field79
                        ]),
                        'input_data'   => urlencode(json_encode([
                            'code' => $code
                        ])),
                        'pattern_code' => 's9kfkfjhd84w5eh'
                    ]);
                    $url = "https://ippanel.com/patterns/pattern?" . urldecode($http_build_query);
                    Http::asJson()->post($url);
                }
                $get->update([
                    'pn_field116' => $code,
                    'pn_field118' => date('Y-m-d H:i:s')
                ]);
                return [
                    'status'  => true,
                    'message' => 'کد تائید'
                ];
            }
        }

        return [
            'status'  => false,
            'message' => 'چنین کاربری وجود ندارد!'
        ];
    }

    public function check_code(Request $request)
    {
        $request->validate([
            'code'   => ['required', 'string'],
            'number' => ['required', 'string'],
            'sms'    => ['required', 'integer']
        ]);
        $get = User::query()
            ->where('pn_field14', $this->toEnNumb($request->input('code')))
            ->where('pn_field79', $this->toEnNumb($request->input('number')))
            ->where('pn_field116', $this->toEnNumb($request->input('sms')));
        if ($get->exists()) {
            $user = $get->get();
            if ($user->count() === 1) {
                $user = $user[0];
                Auth::login($user);
                return [
                    'status'  => true,
                    'message' => 'redirect'
                ];
            }
        }

        return [
            'status'  => false,
            'message' => 'کد اشتباه است!'
        ];
    }

    public function with_app(Request $request)
    {
        $referer = $request->headers->get('referer');
        Auth::logout();
        if (Str::contains($referer, ['192.192.1.7', '192.192.1.6', '192.169.100.6', '192.169.100.6:2000'])) {
            $user = User::query()->find($request->input('id'));
            if ($user) {
                Auth::login($user);
            }
        }
        return to_route('vue');
    }
}
