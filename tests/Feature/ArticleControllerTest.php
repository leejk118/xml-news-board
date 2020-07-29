<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    public function testArticleControllerIndex(){
        $response = $this->get('/articles');

        $response
            ->assertStatus(200)
            ->assertSeeText("Saramin");
    }

    public function testArticleControllerShow(){
        $response = $this->get('/articles/3189');

        $response
            ->assertStatus(200)
            ->assertSee('목록으로');
    }

//    public function testArticleControllerDestroy(){
//        $response = $this->delete("articles", []);
//    }
//
//    public function testArticleControllerDestroyAll(){
//
//    }
//
    public function testArticleControllerEditByGuest(){
        $response = $this->get("/articles/3189/edit");

        $response
            ->assertStatus(302)
            ->assertSee("login");
    }

    public function testArticleControllerEditByAdmin(){
        $user = auth()->loginUsingId(1);

        $response = $this->actingAs($user)->get("/articles/3189/edit");

        $response
            ->assertSee(200);
    }
//
//    public function testArticleControllerUpdate(){
//
//    }

}
