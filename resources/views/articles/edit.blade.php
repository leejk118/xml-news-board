@extends('layouts.app')

@section('script')
    <script type="text/javascript" src="/se2/js/service/HuskyEZCreator.js" charset="utf-8"></script>
@endsection

@section('content')
    <div style="width: 70%; margin-left:auto; margin-right : auto">
        <form action="{{ route('articles.update', $article->id) }}" method="POST" onsubmit="return submitContents(this);">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="queryString" value="{{ $_SERVER['QUERY_STRING'] }}">

            <h3>제목</h3>
            <div class="form-group">
                <input type="text" name="title" class="form-control w-75" value="{{ $article->title }}" autofocus><br>
                {!! $errors->first('title', '<div class="alert alert-danger w-75">:message</div>')!!}
                <br>
            </div>

            <h5>부제목</h5>
            <div class="form-group">
                <input type="text" name="subtitle" class="form-control w-75" value="{{ $article->subtitle }}"><br>
                {!! $errors->first('subtitle', '<div class="alert alert-danger w-75">:message</div>')!!}
            </div>

            <h5>기사링크</h5>
            <div class="form-group">
                <input type="text" name="news_link" class="form-control w-75" value="{{ $article->news_link }}"><br>
                {!! $errors->first('news_link', '<div class="alert alert-danger w-75">:message</div>')!!}
                <br>
            </div>

            <textarea name="content" id="content" rows="10" cols="100" style="height: 500px" >
                {{ $article->content }}
            </textarea>
            <br>
            {!! $errors->first('content', '<div class="alert alert-danger w-75">:message</div>')!!}
            <br><br>

            <button class="btn btn-primary">저장하기</button>
        </form>
    </div>

    <script type="text/javascript">
        var oEditors = [];
        nhn.husky.EZCreator.createInIFrame({
            oAppRef: oEditors,
            elPlaceHolder: "content",
            sSkinURI: "/se2/SmartEditor2Skin.html",
            fCreator: "createSEditor2",
        });

        function submitContents(e){
            if (!confirm("수정하시겠습니까?")){
                return false;
            }
            oEditors.getById["content"].exec("UPDATE_CONTENTS_FIELD", []);
        }
    </script>
@endsection
