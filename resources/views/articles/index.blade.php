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
        <a href="{{ route('articles.index') }}">
            <h1>News</h1>
        </a>
        <hr>
        <button id="deleteAll" onclick="button_click()">일괄삭제</button>
        <table class="table">
            <tr>
                <th>id</th>
                <th>기사 제목</th>
                <th>등록일</th>
                <th>조회수</th>
                <th>admin</th>
            </tr>
            @foreach($articles as $article)
                <tr height="100px">
                    <td>{{ $article->id }}</td>
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
                        {{ $article->view_count }}
                    </td>
                    <td>
                        <form action="{{ route('articles.destroy', [$article->id]) }}" method="post"
                                    onsubmit="return confirm('삭제하시겠습니까?');">
                            {!! csrf_field() !!}
                            <input type="hidden" name="_method" value="delete">
                            <button>삭제</button>
                        </form>
                        <input type="checkbox" value="{{ $article->id }}">
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

            var targetList = [];
            for (var i = 0; i < target.length; ++i){
                targetList.push(target[i].value);
            }

            if (confirm("전부 삭제?")){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url : '{{ route('articles.destroys') }}',
                    type : 'POST',
                    data : JSON.stringify({data : targetList}),
                    success : function () {
                        alert("삭제 성공");
                        window.location.href = '{{ route('articles.index') }}';
                    }
                });
            }


        }
    </script>
@stop
