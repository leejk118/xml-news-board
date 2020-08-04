@extends('layouts.app')

@section('content')
    <div style="width: 50%; margin: auto">
        <h2>로그인</h2>
        <hr>
        <br>
        <form action="{{ route('sessions.store') }}" method="POST">
            {!! csrf_field() !!}

            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                <input type="email" name="email" class="form-control" placeholder="이메일" value="{{ old('email') }}" autofocus/>
                {!! $errors->first('email', '<span class="form-error">:message</span>') !!}
            </div>

            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                <input type="password" name="password" class="form-control" placeholder="비밀번호">
                {!! $errors->first('password', '<span class="form-error">:message</span>')!!}
            </div>

            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="remember" value="{{ old('remember', 1) }}" checked>
                        로그인 기억하기 <span class="text-danger">(공용 컴퓨터에서는 사용 금지)</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-primary btn-lg btn-block" type="submit">
                    로그인하기
                </button>
            </div>

            <div>
                <p class="text-center">회원이 아니라면?
                    <a href="{{ route('users.create') }}">
                        가입하세요.
                    </a>
                </p>
            </div>
        </form>
    </div>
@stop
