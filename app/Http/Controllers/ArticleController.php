<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->input('q') != null){
            $keyword = $request->q;
            $category = $request->category;

            if ($category == 'both') {
                $articles = \App\Article::where('title', 'like', '%' . $keyword  . '%')
                                            ->orWhere('content', 'like', '%' . $keyword  . '%')
                                            ->paginate(10);
            }
            else {
                $articles = \App\Article::where($category, 'like', '%' . $keyword  . '%')
                                            ->paginate(10);
            }

            $articles->withQueryString()->links();
        }
        else {
            $articles = \App\Article::orderBy('send_date', 'desc')->paginate(10);
        }

        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = \App\Article::find($id);

        $article->view_count += 1;
        $article->save();

        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $article = \App\Article::find($id);

        return view('articles.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $content = $request->ir1;
        $previewContent = iconv_substr(preg_replace("/<(.+?)>/", "", $content), 0, 100, "UTF-8");

        \App\Article::where('id', $id)
                    ->update(['title' => $request->title,
                            'content' => $content,
                            'preview_content' => $previewContent]);

        return redirect(route('articles.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Article::destroy($id);

        return redirect(route('articles.index'));
    }

    public function destroys(Request $request){
        $list = json_decode($request->getContent(), true);

        foreach ($list['data'] as $id) {
            \App\Article::destroy($id);
        }

        return redirect(route('articles.index'));
    }
}
