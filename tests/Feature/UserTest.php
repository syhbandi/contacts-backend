<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function register_success(): void
    {
        $this->post('/api/users', [
            'username' => 'syahbandi',
            'name' => 'Ahmad Syahbandi',
            'password' => 'rahasia'
        ])->assertStatus(200)->assertJson([
            'data' => []
        ]);
    }
    public function registerFailed(): void
    {
        $this->post('/api/users', [
            'username' => '',
            'name' => '',
            'password' => ''
        ])->assertStatus(400)->assertJson([
            'errors' => []
        ]);
    }
}
