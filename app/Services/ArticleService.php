<?php

namespace App\Services;

use App\Repositories\ArticleRepository;

class ArticleService
{

    protected $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function getArticles($category, $q)
    {
        switch ($category){
            case "both":
                return $this->articleRepository->getAll();
                break;
            case "title" :
                return $this->articleRepository->getArticleByTitle($q);
                break;
            case "content" :
                return $this->articleRepository->getArticleByContent($q);
                break;
            case null :
                return $this->articleRepository->getAll();
                break;
            default : break;
        }
    }

}
