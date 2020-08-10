<?php

namespace App\Services;

use App\Article;
use App\Repositories\ArticleRepository;


class ArticleService
{
    public function __construct(ArticleRepository $article)
    {
        $this->article = $article;
    }

    public function index($category, $q)
    {
        return $this->article->index($category, $q);
    }
}
