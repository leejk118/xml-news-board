<?php

namespace Tests\Feature;

use App\Article;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;
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
        $this
            ->get('/articles')
            ->assertStatus(200)
            ->assertViewIs('articles.index')
            ->assertViewHas('articles')
            ->assertSee("Saramin News")
            ->assertSee("Register");
    }

    public function testArticleControllerShow()
    {
        $article = factory(Article::class)->create();

        $this
            ->get('articles/' . $article->id)
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


        // 비 로그인 시
        $this
            ->actingAs($user)
            ->put(route('articles.update', $article->id), [$article->id], ['_token' => csrf_token()])
            ->assertStatus(419);
//            ->assertSee('dd');

//        $this->call('Put', 'articles/' . $article->id, [$article->id])
//            ->assertStatus(302)
//            ->assertRedirect('articles/index');
//        $this
//            ->put('articles/' . $article->id, compact('article'), ['_token' => csrf_token()])
//            ->assertStatus(302)
//            ->assertSee('dd');

        // 로그인 시
//        $article->title = 'updated title';
//        $this
//            ->actingAs($user)
//            ->put('articles/' . $article->id, [$article->id], ['_token' => csrf_token()])
//            ->assertStatus(302)
//            ->assertSee('dd');

//        $this->assertDatabaseHas('articles', [
//            'id' => $article->id,
//            'title' => 'updated title'
//        ]);
    }

    public function testArticleControllerDestroy()
    {
        Session::start();

        $article = factory(Article::class)->create();
        $user = factory(User::class)->create([
            'activated' => 1
        ]);

        // 비로그인 시
        $this
            ->delete(route('articles.destroy', $article->id), ['_token' => csrf_token()])
            ->assertRedirect('auth/login');

        // 로그인 시
        $this
            ->actingAs($user)
            ->delete(route('articles.destroy', $article->id), ['_token' => csrf_token()])
            ->assertRedirect('/articles');

        $this
            ->get(route('articles.index'))
            ->assertSee($article->id . '번 글이 삭제 완료되었습니다.')
            ->assertDontSee($article->title);

        $this
            ->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

}
