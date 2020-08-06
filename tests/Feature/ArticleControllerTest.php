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
        $response = $this->get('/');

        $response->assertRedirect('/articles');
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

        $response = $this->get('articles/' . $article->id);

        $response
            ->assertStatus(200)
            ->assertViewHas('article')
            ->assertViewIs('articles.show')
            ->assertSee('목록으로')
            ->assertSee($article->title);
    }

    public function testArticleControllerDestroy()
    {
        Session::start();

        $article = factory(Article::class)->create();
        $user = factory(User::class)->create([
            'activated' => 1
        ]);

        // 비로그인 시
        $this->delete('articles/' . $article->id, ['_token' => csrf_token()])
            ->assertRedirect('auth/login');

        // 로그인 시
        $this->actingAs($user)->delete('articles/' . $article->id, ['_token' => csrf_token()])
            ->assertRedirect('/articles');

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function testArticleControllerEdit()
    {
        $article = factory(Article::class)->create();
        $user = factory(User::class)->create([
            'activated' => 1
        ]);

        // 비로그인 상태 일 때 로그인 창으로 이동
        $this->get('articles/' . $article->id . '/edit')
                    ->assertRedirect('auth/login');

        // 로그인 상태 일 때 폼으로 이동
        $this->actingAs($user)->get('articles/' . $article->id . '/edit')
            ->assertStatus(200)
            ->assertViewIs('articles.edit');
    }

    public function testArticleControllerUpdate()
    {
        $article = factory(Article::class)->create();
        $user = factory(User::class)->create([
            'activated' => 1
        ]);

//        $this->withoutMiddleware();
//         비 로그인 시
        $this->put('articles/' . $article->id, [], ['_token' => csrf_token()])
            ->assertStatus(419);
//            ->assertRedirect('auth/login');


        // 로그인 시
        $article->title = 'updated title';
        $response = $this->actingAs($user)->put('articles/' . $article->id,
                                                    $article->toArray(),
                                                    ['_token' => csrf_token()]);

//        $this->assertDatabaseHas('articles', [
//            'id' => $article->id,
//            'title' => 'updated title'
//        ]);
    }

}
