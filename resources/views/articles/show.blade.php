@extends('layouts.app')

@section('content')
    <div class="container">
        <h1><b>{{ $article->title }}</b></h1>
        <hr>
        <div class="float-left">
            <p>작성일 : {{ $article->send_date }}  </p>
        </div>
        <div class="text-right">
            <p>조회수 : {{ $article->view_count }}</p>
        </div>

        <br>
        <h3>{{ $article->subtitle }}</h3>
        <p>원본링크 : <a href="{{ $article->news_link }}" target="_blank">{{ $article->news_link }}</a></p>
        <br>
        {!!  $article->content  !!}
        <br>
        <br>

        <a href="javascript:window.history.back();">
            <button class="btn btn-primary">목록으로</button>
        </a>
        @auth
            <form action="{{ route('articles.destroy', $article->id) }}" method="post"
                  onsubmit="return confirm('삭제하시겠습니까?');" class="d-inline">
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="delete">
                <button class="btn btn-danger">삭제하기</button>
            </form>

            <form action="{{ route('articles.edit', $article->id) }}" method="GET" class="d-inline">
                <button class="btn btn-dark">수정하기</button>
            </form>
        @endauth
    </div>
@stop
