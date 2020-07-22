@extends('layouts.app')

@section('css')
    <style type="text/css">
        a { color:black; text-decoration:none; }
    </style>
@stop

@section('content')
    <div class="container">
        <h1>News</h1>
        <hr>
        <table class="table">
            <tr>
                <th>기사 제목</th>
                <th>등록일</th>
                <th>조회수 (예정)</th>
            </tr>
            @foreach($articles as $article)
                <tr height="100px">
                    <td width="800px">
                        <a href="articles/{{ $article->id }}">
                            @if (isset($article->preview_img))
                                <div style="float: left; margin-right: 30px">
                                    <img src="{{ $article->preview_img }}" width="120px" height="100px" >
                                </div>
                            @endif
                            <div>
                                <h5>{{ $article->title }}</h5>
                                <p>{{ $article->preview_content }}...</p>
                            </div>
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
