<?php

namespace App\Services;

use App\Repositories\ArticleRepository;

class ArticleService
{
    /**
     * @var ArticleRepository
     */
    protected $articleRepository;

    /**
     * ArticleService constructor.
     * @param ArticleRepository $articleRepository
     */
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param $category
     * @param $q
     * @return \App\Article|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function index($category, $q)
    {
        if ($category == null) {
            return $this->articleRepository->all();
        } else {
            return $this->articleRepository->index($category, $q);
        }
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        $this->articleRepository->show($id)->increment('view_count');

        return $this->articleRepository->show($id);
    }

    /**
     * @param $input
     * @param $id
     */
    public function update($input, $id)
    {
        $input['preview_content'] = iconv_substr(preg_replace(
            "/<(.+?)>/",
            "",
            $input['content']
        ), 0, 100, "UTF-8");

        $this->articleRepository->update($input, $id);

        flash($id . '번 글이 수정 완료되었습니다.');
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->articleRepository->delete($id);

        flash($id . '번 글이 삭제 완료되었습니다.');
    }

    /**
     * @param $list
     */
    public function deleteAll($list)
    {
        $lists = json_decode($list, true);

        foreach ($lists['data'] as $id) {
            $this->articleRepository->delete($id);
        }

        flash('삭제 완료되었습니다.');
    }
}
