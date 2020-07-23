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
        $response = $this->get('/articles');

        $response->assertStatus(200);
    }

    public function indexTest(){
        $response = $this->get('/articles?q=냉장고');
//
        $response->assertStatus(200);
        $response->assertSee('asdf');


    }
}
