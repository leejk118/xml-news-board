@extends('layouts.app')

@section('css')
    <style type="text/css">
        a { color:black; text-decoration:none; }

        .searchForm {
            display: inline-block;
            width:500px;
        }

        .divCenter {
            text-align: center;
        }
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

        <div>
            @if($articles->count())
                {!! $articles->render() !!}
            @endif
        </div>

        <div class="divCenter">
            <form action="{{ route('articles.index') }}" metod="get">
                <select class="form-control " style="width: 200px; display: inline-block" name="category">
                    <option value="both">제목 + 본문</option>
                    <option value="title">제목</option>
                    <option value="content">본문</option>
                </select>
                <input type="text" name="q" class="form-control searchForm" placeholder="기사 검색" />
                <button type="submit" class="btn btn-primary">검색</button>
            </form>
        </div>
    </div>
@stop
