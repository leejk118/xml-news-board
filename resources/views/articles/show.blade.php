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
    </div>
@stop
