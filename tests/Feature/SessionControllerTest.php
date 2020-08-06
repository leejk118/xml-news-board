<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\User;
use Tests\TestCase;

class SessionControllerTest extends TestCase
{
    use WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSessionCreate()
    {
//        $this->withoutExceptionHandling();

        $response = $this->get('auth/login');

        $response->assertStatus(200)
                ->assertViewIs('sessions.create')
                ->assertSee('이메일')
                ->assertSee('비밀번호')
                ->assertsee('로그인하기');
    }

    public function testSessionStore()
    {
        $response = $this->post('auth/login');

        $this->assertGuest();
        $response->assertStatus(419);


        $user = new User([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => bcrypt('secret'),
        ]);

        $response = $this->post('auth/login', [
            'email' => $user->email,
            'password' => 'secret'
        ]);

        $this->withoutMiddleware();

        $this->actingAs($user)
            ->get('auth/login')
            ->assertStatus(200);
//            ->assertSee('Newss');

//        $this->be($user);
//        $this->assertAuthenticatedAs($user);
//        $response->assertStatus(200);

//        $this->assertGuest();
//        $response->assertRedirect('/articles');

//        $this->actingAs($user);


    }

    public function testSessionDestroy()
    {
//        $this->assertAuthenticated();
        $response = $this->get('auth/logout');

        // 로그아웃 체크
        $this->assertGuest();
        $response->assertRedirect('/articles');
    }

}
