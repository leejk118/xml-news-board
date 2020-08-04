@extends('layouts.app')

@section('script')
    <script type="text/javascript" src="/se2/js/service/HuskyEZCreator.js" charset="utf-8"></script>
@endsection

@section('content')
{{--    @if ($errors->any())--}}
{{--        <div class="alert alert-danger">--}}
{{--            <ul>--}}
{{--                @foreach ($errors->all() as $error)--}}
{{--                    <li>{{ $error }}</li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--        </div>--}}
{{--    @endif--}}

    <div class="container">
        <form action="{{ route('articles.update', $article->id) }}" method="POST" onsubmit="return submitContents(this);">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="queryString" value="{{ $_SERVER['QUERY_STRING'] }}">

            <h1>제목</h1>
            <input type="text" style="width: 766px" name="title" value="{{ $article->title }}">
            {!! $errors->first('title', '<span class="form-error">:message</span>')!!}
            <br><br><br>

            <h5>subtitle</h5>
            <input type="text" style="width: 766px" name="subtitle" value="{{ $article->subtitle }}"><br>
            <h5>기사링크</h5>
            <input type="text" style="width: 766px" name="news_link" value="{{ $article->news_link }}"><br><br><br>

            <textarea name="content" id="content" rows="10" cols="100" style="width:766px; height:412px; ">
                {{ $article->content }}
            </textarea>
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
