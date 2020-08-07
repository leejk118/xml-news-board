<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SessionControllerTest extends TestCase
{

    use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSessionControllerCreate()
    {
        $user = factory(User::Class)->create();

        // 비로그인
        $response = $this->get('auth/login');

        $response->assertStatus(200)
                ->assertViewIs('sessions.create')
                ->assertSee('이메일')
                ->assertSee('비밀번호')
                ->assertsee('로그인하기');

        // 로그인 시 인덱스 페이지
        $this->actingAs($user)->get('auth/login')
            ->assertStatus(302)
            ->assertRedirect('articles');
    }

    public function testSessionControllerStore()
    {
        $this->assertGuest();

    }

    public function testSessionControllerDestroy()
    {
        $response = $this->get('auth/logout');

        // 로그아웃 체크
        $this->assertGuest();
        $response->assertRedirect('/articles');
    }

}
