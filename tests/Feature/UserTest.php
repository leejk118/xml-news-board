<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{

    public function test_a_user_cannot_login_while_logged_in()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }
}
