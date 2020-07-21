@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>News</h1>
        <hr>
        <table border="1">
            <tr>
                <th>기사 제목</th>
                <th>등록일</th>
                <th>조회수 (예정)</th>
            </tr>
            @foreach($articles as $article)
                <tr height="100px">
                    <td>
                        <a href="articles/{{ $article->id }}">
                            @if (isset($article->preview_img))
                                <img src="{{ $article->preview_img }}" width="120px" height="100px">
                            @endif
                            {{ $article->title }}
                        </a>
                    </td>
                    <td>
                        {{ $article->send_date }}
                    </td>
                    <td>
                        조회수
                    </td>
                </tr>
            @endforeach
        </table>

        @if($articles->count())
            <div class="text-center">
                {!! $articles->render() !!}
            </div>
        @endif
    </div>
@stop
