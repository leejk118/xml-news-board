@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $article->title }}</h1>
        <hr>
        <h3>{{ $article->subtitle }}</h3>
        <p>작성일 : {{ $article->send_date }}</p>
        <p>원본링크 : <a href="{{ $article->news_link }}" target="_blank">{{ $article->news_link }}</a></p>
        <br><br>
        {!!  $article->content  !!}

        <br>
        <br>

        <div>

            <form action="{{ route('articles.destroy', $article->id) }}" method="post"
                  onsubmit="return confirm('삭제하시겠습니까?');" style="display: inline;">
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="delete">
                <input type="submit" value="삭제하기"/>
            </form>

            <form action="{{ route('articles.edit', $article->id) }}" method="GET"
                  style="display: inline;">
                <input type="submit" value="수정하기"/>
            </form>

        </div>
    </div>
@stop
