@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <style type="text/css">
        a { color:black; text-decoration:none; }

        .searchForm {
            display: inline-block;
            width:500px;
        }

        .divCenter {
            text-align: center;
        }

        .iconBtn {
            font-family: FontAwesome;
            background: none;
            border: none;
        }
    </style>
@stop

@section('content')
    <div class="container">
        <a href="{{ route('articles.index') }}">
            <h1>News</h1>
        </a>
        <hr>

        @auth
            <div style="text-align: right">
                <button id="deleteAll" onclick="button_click()" class="btn btn-danger">일괄삭제</button>
            </div>
        @endauth
        <br>

        <table class="table"  style="text-align: center">
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
                    <td width="700px" style="text-align: left;">
                        <a href="articles/{{ $article->id }}">
                            @if (isset($article->preview_img))
                                <div style="float: left; margin-right: 30px">
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
                            <form action="{{ route('articles.destroy', $article->id) }}" method="post"
                                        onsubmit="return confirm('삭제하시겠습니까?');" style="display: inline;">
                                {!! csrf_field() !!}
                                <input type="hidden" name="_method" value="delete">
                                <input class="iconBtn" style="color:red;" type="submit" value="&#xf1f8">
                            </form>

                            <form action="{{ route('articles.edit', $article->id) }}" method="GET"
                                    style="display: inline;">
                                <input class="iconBtn" style="color:green;" type="submit" value="&#xf044">
                            </form>

                            <input type="checkbox" value="{{ $article->id }}">
                        </td>
                    @endauth
                </tr>
            @endforeach
        </table>

        <div style="width: 525px; margin: auto">
            @if($articles->count())
                {!! $articles->render() !!}
            @endif
        </div>


        <div class="divCenter">
            <form action="{{ route('articles.index') }}" method="get">
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

@section('script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>
        function button_click(){
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
