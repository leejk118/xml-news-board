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

    public function getAll()
    {
        return $this->article->paginate(10);
    }

    public function getArticleByTitle($q)
    {
        return $this->article->where('title', 'like', '%' . $q . '%')->get();
    }

    public function getArticleByContent($q)
    {
        return $this->article->whereContent($q)->get();
    }

    public function getArticleByTitleAndContent($q)
    {
        return $this->article->get();
    }

}
