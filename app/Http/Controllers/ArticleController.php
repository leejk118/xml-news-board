<?php

namespace App\Http\Controllers;

use App\Exceptions\PageOutOfBoundException;
use App\Services\ArticleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use App\Article;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

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
     * @return View
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
     * @return View
     */
    public function show(Article $articles)
    {
        $article = $this->articleService->show($articles->id);

        return view('articles.show', ['article' => $article, 'queryString' => request()->getQueryString()]);
    }

    /**
     * @param Article $article
     * @return View
     */
    public function edit(Article $article)
    {
        return view('articles.edit', ['article' => $article, 'queryString' => request()->getQueryString()]);
    }

    /**
     * @param ArticleRequest $request
     * @param Article $article
     * @return RedirectResponse|Redirector
     */
    public function update(ArticleRequest $request, Article $article)
    {
        $this->articleService->update($request->only('title', 'subtitle', 'news_link', 'content'), $article->id);

        return redirect(route('articles.show', [$article->id, $request->queryString]));
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return RedirectResponse|Redirector
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
