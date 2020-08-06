@extends('layouts.app')

@section('content')
    <div class="card w-50 mx-auto">
        <div class="card-header">
            {{ $title }}
        </div>
        <div class="card-body">
            {{ $description }}
        </div>
        <div class="mx-auto">
            <a href="/">
                <button class="btn btn-primary">홈으로 이동</button>
            </a>
        </div>
        <br>
    </div>

@stop
