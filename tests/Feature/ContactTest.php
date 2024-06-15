<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_create_contact(): void
    {
        $this->seed(UserSeeder::class);
        $response = $this->post('/api/contacts', [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test',
            'phone' => '123',
        ], [
            'Authorization' => 'test'
        ]);

        $response->assertStatus(201);
    }

    public function test_get_contact(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $response = $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'test'
        ]);

        $response->assertStatus(200);
    }

    public function test_update_contact(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $response = $this->put('/api/contacts/' . $contact->id, ['first_name' => 'toast'], [
            'Authorization' => 'test'
        ]);

        $response->assertStatus(200);
    }

    public function test_delete_contact(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $response = $this->delete('/api/contacts/' . $contact->id, [], [
            'Authorization' => 'test'
        ]);

        $response->assertStatus(200);
    }

    public function test_search_contact(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $response = $this->get('/api/contacts?query=test', [
            'Authorization' => 'test'
        ]);

        $response->assertStatus(200);
    }
}
