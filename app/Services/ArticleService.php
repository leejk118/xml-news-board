<?php

namespace App\Services;

use App\Repositories\ArticleRepository;

class ArticleService
{
    public function __construct(ArticleRepository $article)
    {
        $this->article = $article;
    }

    public function index($category, $q)
    {
        if ($category == null) {
            return $this->article->all();
        }
        else {
            return $this->article->index($category, $q);
        }
    }

    public function show(int $id)
    {
        $this->article->show($id)->increment('view_count');

        return $this->article->show($id);
    }

    public function update($input, $id)
    {
        $input['preview_content'] = iconv_substr(preg_replace(
            "/<(.+?)>/",
            "",
            $input['content']
        ), 0, 100, "UTF-8");

        $this->article->update($id, $input);
    }

    public function delete($id)
    {
        $this->article->delete($id);
    }

    public function deleteAll($list)
    {
        $lists = json_decode($list, true);

        foreach ($lists['data'] as $id){
            $this->article->delete($id);
        }
    }
}
