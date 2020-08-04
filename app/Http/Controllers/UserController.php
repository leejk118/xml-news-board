<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\User;
use App\Http\Requests\UserRequest;
use App\Events\UserCreated;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        $confirmCode = Str::random(60);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'confirm_code' => $confirmCode
        ]);

        event(new UserCreated($user));

        flash('가입하신 계정으로 가입 확인 메일 전송. 확인하고 로그인 해주세요.');

        return redirect(route('articles.index'));
    }

    public function confirm($code)
    {
        $user = User::whereConfirmCode($code)->first();

        if (!$user) {
            flash('URL이 정확하지 않습니다.');

            return redirect(route('articles.index'));
        }

        $user->activated = 1;
        $user->confirm_code = null;
        $user->save();

        Auth::login($user);
        flash(Auth::user()->name . '님. 환영합니다. 가입 확인되었습니다.');

        return redirect(route('articles.index'));
    }
}
