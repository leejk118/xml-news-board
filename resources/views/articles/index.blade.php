@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>News</h1>
        <hr>
        <table>
            <tr>
                <th>기사 제목</th>
                <th>등록일</th>
                <th>조회수 (예정)</th>
            </tr>
            @foreach($articles as $article)
                <tr>
                    <td>
                        <a href="articles/{{ $article->id }}">
                            (미리보기)
                            {{ $article->title }}
                        </a>
                    </td>
                    <td>
                        {{ $article->send_date }}
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
