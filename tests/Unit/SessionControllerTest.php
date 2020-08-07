<?php

namespace Tests\Unit;

use App\Http\Middleware\VerifyCsrfToken;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SessionControllerTest extends TestCase
{

    use DatabaseTransactions;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::Class)->create([
            'activated' => 1
        ]);
    }

    public function testSessionControllerCreate()
    {
        // 비로그인
        $response = $this->get('auth/login');

        $response->assertStatus(200)
                ->assertViewIs('sessions.create')
                ->assertSee('이메일')
                ->assertSee('비밀번호')
                ->assertsee('로그인하기');

        // 로그인 시 인덱스 페이지
        $this->actingAs($this->user)->get('auth/login')
            ->assertStatus(302)
            ->assertRedirect('articles');
    }

    public function testSessionControllerStore()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->post(route('sessions.store'), [
            'email' => 'aaa@aaa',
            'password' => 'aaaaaa'
        ]);

        $response
            ->assertStatus(302)
            ->assertRedirect(route('articles.index'));

        $this
            ->get(route('articles.index'))
            ->assertSee('님. 환영합니다.');
    }

    public function testSessionControllerDestroy()
    {
        $response = $this->actingAs($this->user)->get(route('articles.index'));

        // 로그인 상태 확인
        $response
            ->assertStatus(200)
            ->assertSee('logout')
            ->assertDontSee('login')
            ->assertDontSee('Register');

        // 로그 아웃 실행
        $response = $this->get(route('sessions.destroy'));

        $response
            ->assertStatus(302)
            ->assertRedirect(route('articles.index'));

        $this
            ->get(route('articles.index'))
            ->assertStatus(200)
            ->assertSee('로그아웃 되었습니다.')
            ->assertSee('login')
            ->assertSee('register')
            ->assertDontSee('logout');
    }

}
