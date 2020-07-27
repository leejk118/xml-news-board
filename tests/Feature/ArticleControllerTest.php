<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->getJson('/articles', ['q' => '전자', 'category' => 'both']);

//        $response = $this->getJson('/articles');

        $response
            ->assertStatus(200)
            ->assertJson(['title' => "ss"]);

        $content = $this->call('GET', '/articles')->getContent();

    }

}
