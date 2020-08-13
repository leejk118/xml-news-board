<?php

namespace App\Http\Controllers;

use App\Exceptions\PageOutOfBoundException;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use App\Article;

class ArticleController extends Controller
{
    /**
     * @var ArticleService
     */
    protected $articleService;

    /**
     * ArticleController constructor.
     * @param ArticleService $articleService
     */
    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;

        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     * @throws PageOutOfBoundException
     */
    public function index(Request $request)
    {
        $articles = $this->articleService->index($request->category, $request->q);

        if ($request->page > $articles->lastPage() || $request->page < 0) {
            throw new PageOutOfBoundException("Out of Page!");
        }

        return view('articles.index', compact('articles'));
    }


    /**
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(Article $articles)
    {
        $article = $this->articleService->show($articles->id);

        return view('articles.show', ['article' => $article, 'queryString' => request()->getQueryString()]);
    }

    /**
     * @param Article $article
     * @return \Illuminate\View\View
     */
    public function edit(Article $article)
    {
        return view('articles.edit', ['article' => $article, 'queryString' => request()->getQueryString()]);
    }

    /**
     * @param ArticleRequest $request
     * @param Article $article
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(ArticleRequest $request, Article $article)
    {
        $this->articleService->update($request->only('title', 'subtitle', 'news_link', 'content'), $article->id);

        return redirect(route('articles.show', [$article->id, $request->queryString]));
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function destroy(Request $request, Article $article)
    {
        $this->articleService->delete($article->id);

        return redirect(route('articles.index', $request->queryString));
    }

    /**
     * 일괄 삭제 기능
     *
     * @param Request $request
     */
    public function destroys(Request $request)
    {
        $this->articleService->deleteAll($request->getContent());
    }
}
