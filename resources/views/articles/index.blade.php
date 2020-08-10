@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <style type="text/css">
        .searchForm {
            display: inline-block;
            width:500px;
        }
        .iconBtn {
            font-family: FontAwesome;
            background: none;
            border: none;
        }
        .pagingList {
            width : max-content;
            margin : auto;
        }
    </style>
@stop

@section('content')
    <div class="container">
        <div class="container">
            <h3>어제의 주요뉴스</h3>
            <hr>
            <div class="text-center">
                @foreach($newsHistories as $newsHistory)
                    <a href="{{ route('articles.show', [$newsHistory->article->id, $_SERVER['QUERY_STRING']]) }}" class="text-dark">
                        <div class="card d-inline-block" style="width: 19%;">
                            @if (isset($newsHistory->article->preview_img))
                                <img class="card-img-top" src="{{ $newsHistory->article->preview_img }}"  height="120px" >
                                <br>
                            @endif
                            <div class="card-body text-left">
                                <p class="card-text">{{ iconv_substr($newsHistory->article->title, 0, 25, "UTF-8") }}...</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <br><br>
        <a href="{{ route('articles.index') }}">
            <h1>News</h1>
        </a>
        <hr>

        @auth
            <div class="text-right">
                <button id="selectBox" onclick="selectAll()" class="btn btn-secondary">전체선택</button>
                <button onclick="deleteAll()" class="btn btn-danger">일괄삭제</button>
            </div>
        @endauth
        <br>

        <table class="table text-center">
            <tr>
                <th>기사</th>
                <th>등록일</th>
                <th>조회수</th>
                @auth
                    <th>admin</th>
                @endauth
            </tr>
            @foreach($articles as $article)
                <tr height="80px">
                    <td width="700px" class="text-left">
                        <a href="{{ route('articles.show', [$article->id,
                                isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ""]) }}" class="text-dark" >
                            @if (isset($article->preview_img))
                                <div class="float-left mr-3">
                                    <img src="{{ $article->preview_img }}" width="120px" height="100px" >
                                </div>
                            @endif
                            <div>
                                <h5><b>{{ $article->title }}</b></h5>
                                <p>{{ $article->preview_content }}...</p>
                            </div>
                        </a>
                    </td>
                    <td>
                        {{ $article->send_date }}
                    </td>
                    <td>
                        {{ $article->view_count }}
                    </td>
                    @auth
                        <td>
                            <form action="{{ route('articles.destroy', $article->id) }}" method="POST"
                                        onsubmit="return confirm('삭제하시겠습니까?');" class="d-inline">
                                {!! csrf_field() !!}
                                <input type="hidden" name="queryString" value="{{ isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : "" }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <input class="iconBtn text-info" type="submit" value="&#xf1f8">
                            </form>
                            <a href="{{ route('articles.edit', [$article->id, isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ""]) }}">
                                <input class="iconBtn text-success" type="submit" value="&#xf044">
                            </a>
                            <input type="checkbox" value="{{ $article->id }}">
                        </td>
                    @endauth
                </tr>
            @endforeach
        </table>

        <div class="pagingList">
            @if($articles->count())
                {!! $articles->appends(Request::except('page'))->render() !!}
            @endif
        </div>

        <div class="text-center">
            <form action="{{ route('articles.index') }}" method="GET">
                <select class="form-control d-inline w-auto" name="category">
                    <option value="both">제목 + 본문</option>
                    <option value="title">제목</option>
                    <option value="content">본문</option>
                </select>
                <input type="text" name="q" class="form-control searchForm" placeholder="검색어 입력" />
                <button type="submit" class="btn btn-primary">검색</button>
            </form>
        </div>
    </div>
@stop


@section('script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>
        function selectAll(){
            var current = $("#selectBox").html();
            var btnValue = (current == "전체선택") ? "선택해제" : "전체선택";
            var checked = (current == "전체선택") ? true : false;

            $("input[type='checkbox']").prop("checked", checked);
            $("#selectBox").html(btnValue);
        }

        function deleteAll(){
            var target = $("input[type='checkbox']").filter(':checked');

            if (target.length == 0) {
                alert("선택된 항목이 없습니다.");
                return ;
            }

            var targetList = [];
            var length = target.length;
            for (var i = 0; i < length; ++i){
                targetList.push(target[i].value);
            }

            if (confirm(length + "개의 데이터를 삭제 하시겠습니까?")){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url : '{{ route('articles.destroys') }}',
                    type : 'POST',
                    data : JSON.stringify({data : targetList}),
                    success : function () {
                        alert("삭제 성공");
                        window.location.reload();
                    }
                });
            }
        }
    </script>
@stop
