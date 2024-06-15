<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CobaTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    public function test_register_success(): void
    {
        $this->post('/api/users', [
            'username' => 'test',
            'name' => 'test',
            'password' => 'test'
        ])->assertStatus(201)->assertJson([
            'data' => []
        ]);
    }

    public function test_register_failed(): void
    {
        $this->post('/api/users', [
            'username' => '',
            'name' => '',
            'password' => ''
        ])->assertStatus(302);
    }

    public function test_register_duplicate_username(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users', [
            'username' => 'test',
            'name' => 'test',
            'password' => 'test'
        ])->assertStatus(401);
    }

    public function test_login_success(): void
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test'
        ])->assertStatus(200);
    }

    public function test_login_validation(): void
    {
        $this->post('/api/users/login', [
            'username' => '',
            'password' => 'test'
        ])->assertStatus(302);
    }

    public function test_login_failed(): void
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => 'tost',
            'password' => 'test'
        ])->assertStatus(401);
    }

    public function test_get_current_user(): void
    {
        $this->seed(UserSeeder::class);
        $this->get('/api/users/current',  [
            'Authorization' => 'test'
        ])->assertStatus(200);
    }

    public function test_get_current_user_failed(): void
    {
        $this->seed(UserSeeder::class);
        $this->get('/api/users/current', [
            'Authorization' => ''
        ])->assertStatus(401);
    }

    public function test_update_current_user()
    {
        $this->seed(UserSeeder::class);
        $this->patch('/api/users', [
            'name' => 'test2'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200);
    }

    public function test_logout()
    {
        $this->seed(UserSeeder::class);
        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'test'
        ])->assertStatus(200);
    }
}
