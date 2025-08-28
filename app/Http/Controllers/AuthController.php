<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * معالجة طلب تسجيل الدخول
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {

            // check if user is active
            // if (!Auth::user()->is_active) {
            //     Auth::logout();
            //     return redirect()->back()
            //         ->withInput($request->except('password'))
            //         ->with('error', 'حسابك غير نشط، يرجى التواصل مع المسؤول');
            // }
            

            $request->session()->regenerate();
            
            return redirect()->intended(route('dashboard'))
                ->with('success', 'مرحباً بك، تم تسجيل الدخول بنجاح');
        }

        return redirect()->back()
            ->withInput($request->except('password'))
            ->with('error', 'البريد الإلكتروني أو كلمة المرور غير صحيحة');
    }
}