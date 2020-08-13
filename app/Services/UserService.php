<?php

namespace App\Services;

use App\Events\UserCreated;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserService
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserService constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     *
     * @param $request
     */
    public function store($request)
    {
        $confirmCode = Str::random(60);

        $request['confirm_code'] = $confirmCode;
        $request['password'] = bcrypt($request['password']);

        $user = $this->userRepository->create($request);

        event(new UserCreated($user));

        flash('가입하신 계정으로 가입 확인 메일 전송. 확인하고 로그인 해주세요.');
    }

    public function confirm($code)
    {
        $user = $this->userRepository->findByConfirmCode($code);

        if (!$user) {
            flash('URL이 정확하지 않습니다.');
        }
        else {
            $data = [
                'activated' => 1,
                'confirm_code' => null
            ];
            $this->userRepository->update($data, $user->id);

            Auth::login($user);

            flash(Auth::user()->name . '님. 환영합니다. 가입 확인되었습니다.');
        }
    }

}
