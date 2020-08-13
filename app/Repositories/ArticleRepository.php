<?php

namespace App\Repositories;

use App\Article;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleRepository implements RepositoryInterface
{
    /**
     *
     * @var Article
     */
    protected $article;

    /**
     * ArticleRepository constructor.
     *
     * @param Article $article
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * @return LengthAwarePaginator
     */
    public function all()
    {
        return $this->article
                    ->orderBy('id', 'desc')
                    ->paginate(10);
    }

    /**
     * @param $category
     * @param $q
     * @return Article
     */
    public function index($category, $q)
    {
        switch ($category) {
            case 'both':
                return $this->article
                                ->where('title', 'like', '%'. $q . '%')
                                ->orWhere('content', 'like', '%'. $q . '%')
                                ->paginate(10);
            case 'title':
            case 'content':
                return $this->article
                                ->where($category, 'like', '%'. $q . '%')
                                ->paginate(10);
            default:
                return $this->article;
        }
    }

    public function create($data)
    {
        //
    }

    /**
     *
     *
     * @param array $data
     * @param $id
     */
    public function update($data, $id)
    {
        return $this->article->find($id)->update($data);
    }

    /**
     *
     *
     * @param $id
     */
    public function delete($id)
    {
        return $this->article->find($id)->delete();
    }

    /**
     *
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->article->find($id);
    }
}
