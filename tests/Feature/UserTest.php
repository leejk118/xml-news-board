<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function testRegisterValidation()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $this->post(route('users.store'), [
            'name' => '',
            'email' => $this->faker->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertSessionHasErrors('name');

        $this->post(route('users.store'), [
            'name' => $this->faker->name,
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertSessionHasErrors('email');

        $this->post(route('users.store'), [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
            'password_confirmation' => 'passwords'
        ])->assertSessionHasErrors('password');
    }

    public function test()
    {

    }
}
