@extends('layouts.app')

@section('content')

    <div style="width: 50%; margin: auto">
        <h2>회원가입</h2>
        <hr>
        <br>
        <form action="{{ route('users.store') }}" method="POST">
            {!! csrf_field() !!}

            이름
            <div class="form-group {{ $errors->has('name') ? 'alert alert-danger' : '' }}">
                <input type="text" name="name" class="form-control" placeholder="이름" value="{{ old('name') }}" autofocus/>
                {!! $errors->first('name', '<div class="form-error">:message</div>') !!}
            </div>

            이메일
            <div class="form-group {{ $errors->has('email') ? 'alert alert-danger' : '' }}">
                <input type="email" name="email" class="form-control" placeholder="이메일" value="{{ old('email') }}"/>
                {!! $errors->first('email', '<div class="form-error">:message</div>') !!}
            </div>

            비밀번호
            <div class="form-group {{ $errors->has('password') ? 'alert alert-danger' : '' }}">
                <input type="password" name="password" class="form-control" placeholder="패스워드"/>
                {!! $errors->first('password', '<div class="form-error">:message</div>') !!}
            </div>

            비밀번호 확인
            <div class="form-group {{ $errors->has('password_confirmation') ? 'alert alert-danger' : '' }}">
                <input type="password" name="password_confirmation" class="form-control" placeholder="패스워드 확인" />
                {!! $errors->first('password_confirmation', '<div class="form-error">:message</div>') !!}
            </div>
            <br>

            <div class="form-group">
                <button class="btn btn-primary btn-lg btn-block" type="submit">
                    가입하기
                </button>
            </div>
        </form>
    </div>

@stop
