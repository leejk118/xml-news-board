@extends('layouts.app')

@section('script')
    <script type="text/javascript" src="/se2/js/service/HuskyEZCreator.js" charset="utf-8"></script>
@endsection

@section('content')
    <div class="container">
        <form action="{{ route('articles.update', $article->id) }}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="PUT">
            제목
            <input type="text" class="input-group" name="title" value="{{ $article->title }}"><br><br>

            <textarea name="ir1" id="ir1" rows="10" cols="100" style="width:766px; height:412px;">
                {{ $article->content }}
            </textarea>
            <input type="submit" value="저장하기" onclick="submitContents(this)"/>
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
            oEditors.getById["ir1"].exec("UPDATE_CONTENTS_FIELD", []);

            try {
                e.form.submit();
            }
            catch(ex){
            }
        }
    </script>
@endsection
