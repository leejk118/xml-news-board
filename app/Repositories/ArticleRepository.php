<?php

namespace App\Repositories;

use App\Article;

class ArticleRepository
{
    protected $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function all()
    {
        return $this->article
                    ->orderBy('id', 'desc')
                    ->paginate(10);
    }

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

    public function show(int $id)
    {
        return $this->article->find($id);
    }

    public function update($id, $input)
    {
        $this->article->find($id)->update($input);
    }

    public function delete($id)
    {
        $this->article->find($id)->delete();
    }
}
