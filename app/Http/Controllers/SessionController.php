<?php

namespace App\Http\Controllers;

use App\Http\Requests\SessionRequest;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'destroy']);
    }

    public function create()
    {
        return view('sessions.create');
    }

    public function store(SessionRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'), $request->has('remember'))) {
            return $this->respondError('이메일 또는 비밀번호가 맞지 않습니다.');
        }

        if (!Auth::user()->activated) {
            Auth::logout();

            return $this->respondError("확인 메일을 통해 가입 확인해 주세요.");
        }

        flash(Auth::user()->name . '님. 환영합니다.');

        return redirect()->intended(route('articles.index'));
    }

    public function destroy()
    {
        Auth::logout();
        flash('로그아웃 되었습니다.');

        return redirect(route('articles.index'));
    }

    public function respondError($message)
    {
        flash()->error($message);

        return back()->withInput();
    }
}
