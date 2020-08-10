<?php

namespace Tests\Unit;

use App\Http\Middleware\VerifyCsrfToken;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker, WithoutEvents;

    public function testUserControllerCreate()
    {
        $response = $this->get(route('users.create'));

        $response
            ->assertStatus(200)
            ->assertViewIs('users.create')
            ->assertSee('회원가입');
    }

    public function testUserControllerStore()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->post(route('users.store'), [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response
            ->assertStatus(302)
            ->assertRedirect(route('articles.index'));

        $this
            ->get(route('articles.index'))
            ->assertStatus(200)
            ->assertSee('가입하신 계정으로 가입 확인 메일 전송. 확인하고 로그인 해주세요.');
    }

    public function testUserControllerConfirm()
    {
        $confirmCode = Str::random(60);

        $user = factory(User::class)->create([
            'activated' => 0,
            'confirm_code' => $confirmCode
        ]);

        $response = $this->get(route('users.confirm', $confirmCode));

        $response
            ->assertStatus(302)
            ->assertRedirect(route('articles.index'));

        $this
            ->get(route('articles.index'))
            ->assertSee($user->name . "님. 환영합니다. 가입 확인되었습니다.");
    }
}
