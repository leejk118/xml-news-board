@extends('layouts.app')

@section('script')
    <script type="text/javascript" src="/se2/js/service/HuskyEZCreator.js" charset="utf-8"></script>
@endsection

@section('content')
    <div class="container">
        <form action="{{ route('articles.update', $article->id) }}" method="POST" onsubmit="return submitContents(this);">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="queryString" value="{{ $_SERVER['QUERY_STRING'] }}">
            <h1>제목</h1>
            <input type="text" style="width: 766px" name="title" value="{{ $article->title }}"><br><br><br>

            <textarea name="ir1" id="ir1" rows="10" cols="100" style="width:766px; height:412px; ">
                {{ $article->content }}
            </textarea>
            <button class="btn btn-primary">저장하기</button>
        </form>
    </div>

    <script type="text/javascript">
        var oEditors = [];
        nhn.husky.EZCreator.createInIFrame({
            oAppRef: oEditors,
            elPlaceHolder: "ir1",
            sSkinURI: "/se2/SmartEditor2Skin.html",
            fCreator: "createSEditor2",
        });

        function submitContents(e){
            if (!confirm("수정하시겠습니까?")){
                return false;
            }
            oEditors.getById["ir1"].exec("UPDATE_CONTENTS_FIELD", []);
            alert("수정완료");
        }
    </script>
@endsection
