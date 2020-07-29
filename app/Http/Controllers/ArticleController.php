<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $articles = \App\Article::where(function ($query) use ($request) {
            if ($request->q != null) {
                $category = $request->category;
                if ($category == 'both') {
                    $query->orWhere('title', 'like', '%' . $request->q  . '%');
                    $query->orWhere('content', 'like', '%' . $request->q  . '%');
                } elseif ($category == 'title' || $category == 'content') {
                    $query->orWhere($request->category, 'like', '%' . $request->q  . '%');
                }
            }
        })->orderBy('id', 'desc')->paginate(10);

        if ($request->page > $articles->lastPage()) {
            return redirect(route('articles.index'));
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
        return "<h1> create Page</h1>";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return "<h1>store page</h1>";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(\App\Article $article)
    {
        $queryString = request()->getQueryString();
        $article->view_count += 1;
        $article->save();

        return view('articles.show', compact('article', 'queryString'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(\App\Article $article)
    {
        $queryString = request()->getQueryString();

        return view('articles.edit', compact('article', 'queryString'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, \App\Article $article)
    {
        $content = $request->ir1;
        $previewContent = iconv_substr(preg_replace("/<(.+?)>/", "", $content), 0, 100, "UTF-8");

        $article->title = $request->title;
        $article->content = $content;
        $article->preview_content = $previewContent;

        $article->save();

        return redirect(route('articles.show', [$article->id, $request->queryString]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, \App\Article $article)
    {
        $article->delete();

        return redirect('articles?' . $request->queryString);
    }

    public function destroys(Request $request)
    {
        $list = json_decode($request->getContent(), true);

        foreach ($list['data'] as $id) {
            \App\Article::destroy($id);
        }
    }
}
