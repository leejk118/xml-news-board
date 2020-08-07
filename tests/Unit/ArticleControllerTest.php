<?php

namespace Tests\Unit;

use App\Article;
use App\User;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{

    use DatabaseTransactions;

    public function testRootRedirectToArticleIndex()
    {
        $this
            ->get('/')
            ->assertRedirect(route('articles.index'));
    }

    public function testArticleControllerIndex()
    {
        $response = $this->get('/articles');

        $response
            ->assertStatus(200)
            ->assertViewIs('articles.index')
            ->assertViewHas('articles')
            ->assertSee("Saramin News")
            ->assertSee("Register");
    }

    public function testArticleControllerShow()
    {
        $article = factory(Article::class)->create();

        $response =  $this->get(route('articles.show', $article->id));

        $response
            ->assertStatus(200)
            ->assertViewHas('article')
            ->assertViewIs('articles.show')
            ->assertSee('목록으로')
            ->assertSee($article->title);
    }

    public function testArticleControllerEdit()
    {
        $article = factory(Article::class)->create();
        $user = factory(User::class)->create([
            'activated' => 1
        ]);

        // 비로그인 상태 일 때 로그인 창으로 이동
        $this
            ->get(route('articles.edit', $article->id))
            ->assertRedirect('auth/login');

        // 로그인 상태 일 때 edit 폼으로 이동
        $this
            ->actingAs($user)
            ->get(route('articles.edit', $article->id))
            ->assertStatus(200)
            ->assertViewIs('articles.edit');
    }

    public function testArticleControllerUpdate()
    {
        $article = factory(Article::class)->create();
        $user = factory(User::class)->create([
            'activated' => 1
        ]);

        $updatedArticle = [
            'title' => '업데이트된 타이틀',
            'content' => '업데이트 컨텐트',
            'subtitle' => '부제목 업데이트',
            'news_link' => 'http://update.com'
        ];

        $this->withoutMiddleware(VerifyCsrfToken::class);

        // 비 로그인 시
        $this
            ->put(route('articles.update', $article->id), $updatedArticle)
            ->assertStatus(302)
            ->assertRedirect('auth/login');

        // 로그인 시
        $this
            ->actingAs($user)
            ->put(route('articles.update', $article->id), $updatedArticle)
            ->assertStatus(302)
            ->assertRedirect(route('articles.show', $article->id));

        $this
            ->get(route('articles.show', $article->id))
            ->assertStatus(200)
            ->assertSee($article->id . "번 글이 수정 완료되었습니다.")
            ->assertSee($updatedArticle['title'])
            ->assertSee($updatedArticle['content'])
            ->assertSee($updatedArticle['subtitle'])
            ->assertSee($updatedArticle['news_link']);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => $updatedArticle['title'],
            'content' => $updatedArticle['content'],
            'subtitle' => $updatedArticle['subtitle'],
            'news_link' => $updatedArticle['news_link']
        ]);
    }

    public function testArticleControllerDestroy()
    {
        $article = factory(Article::class)->create();
        $user = factory(User::class)->create([
            'activated' => 1
        ]);

        $this
            ->withoutMiddleware(VerifyCsrfToken::class);

        // 비로그인 시
        $this
            ->delete(route('articles.destroy', $article->id))
            ->assertRedirect('auth/login');

        // 로그인 시
        $this
            ->actingAs($user)
            ->delete(route('articles.destroy', $article->id))
            ->assertRedirect('articles');

        $this
            ->get(route('articles.index'))
            ->assertSee($article->id . '번 글이 삭제 완료되었습니다.')
            ->assertDontSee($article->title);

        $this
            ->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

}
