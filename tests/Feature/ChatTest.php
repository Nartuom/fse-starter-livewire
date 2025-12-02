<?php

namespace Tests\Feature;

use App\Livewire\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;
class ChatTest extends TestCase
{
    use RefreshDatabase;
    public function test_authenticated_users_can_send_chat_messages(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        Livewire::actingAs($user)
            ->test(Chat::class)
            ->set('userName', 'Tom Tester')
            ->set('messageText', 'Hello everyone')
            ->call('sendMessage')
            ->assertSet('messageText', ''); 

        //Assert
        $this->assertDatabaseHas('messages', [
            'user_id'   => $user->id,
            'user_name' => 'Tom Tester',
            'body'      => 'Hello everyone',
        ]);
    }

    public function test_message_text_is_required(): void
    {
        //Arrange
        $user = User::factory()->create();

        //Act
        Livewire::actingAs($user)
            ->test(Chat::class)
            ->set('userName', 'Tom Tester')
            ->set('messageText', '')
            ->call('sendMessage')
            ->assertHasErrors(['messageText' => 'required']);

        // Don't store messages
        $this->assertDatabaseCount('messages', 0);
    }

    public function test_avatar_image_can_be_saved(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Fake the storage and image
        Storage::fake('public');
        $fakeImage = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');


        // Act
        Livewire::actingAs($user)
            ->test(Chat::class)
            ->set('avatarImage', $fakeImage)
            ->call('saveAvatar');

        // Assert database changed
        $this->assertNotNull($user->fresh()->avatar_url);

        // Assert the file was stored in the right directory
        Storage::disk('public')->assertExists($user->fresh()->avatar_url);
    }
}
