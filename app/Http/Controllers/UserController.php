<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        $this->middleware('guest');
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        $this->userService->store($request->only('name', 'email', 'password'));

        return redirect(route('articles.index'));
    }

    public function confirm($code)
    {
        $this->userService->confirm($code);

        return redirect(route('articles.index'));
    }
}
