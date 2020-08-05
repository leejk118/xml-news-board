<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use Illuminate\Http\Request;
use App\Article;
use App\Http\Requests\ArticleRequest;

class ArticleController extends Controller
{

//    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
//        $this->articleService = $articleService;

        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
//        $articles = $this->articleService->getArticles($request->category, $request->q);


        $articles = Article::category($request->category, $request->q)
                            ->orderBy('id', 'desc')
                            ->paginate(10);
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
    public function show(Article $article)
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
    public function edit(Article $article)
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
    public function update(ArticleRequest $request, Article $article)
    {
        $previewContent = iconv_substr(preg_replace("/<(.+?)>/", "",
                                            $request->all()['content']), 0, 100, "UTF-8");
        $request->merge(['preview_content' => $previewContent]);

        $article->update($request->all());

        flash($article->id . '번 글이 수정 완료되었습니다.');

        return redirect(route('articles.show', [$article->id, $request->queryString]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Article $article)
    {
        $article->delete();

        flash($article->id . '번 글이 삭제 완료되었습니다.');

        return redirect(route('articles.index', $request->queryString));
    }

    public function destroys(Request $request)
    {
        $list = json_decode($request->getContent(), true);

        foreach ($list['data'] as $id) {
            Article::destroy($id);
        }
    }
}
