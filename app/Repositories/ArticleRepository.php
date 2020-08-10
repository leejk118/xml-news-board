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

    public function index($category, $q)
    {
        switch ($category) {
            case 'both':
                $this->article->orWhere('title', 'like', '%'. $q . '%');
                $this->article->orWhere('content', 'like', '%'. $q . '%');
                break;
            case 'title':
            case 'content':
                $this->article->where($category, 'like', '%'. $q . '%');
                break;
            default:
                break;
        }
        return $this->article->orderBy('id', 'desc')->paginate(15);
    }
}
