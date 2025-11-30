<?php

// app/Livewire/Chat.php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{
    public string $userName = '';
    public string $messageText = '';
    public $chatMessages;

    protected $rules = [
        'userName'    => 'required|string|max:255',
        'messageText' => 'required|string|max:2000',
    ];

    public function mount(): void
    {
        // Default username from authed user (can be overridden)
        if (Auth::check() && $this->userName === '') {
            $this->userName = Auth::user()->name ?? '';
        }

        $this->loadMessages();
    }

    public function loadMessages(): void
    {
        $this->chatMessages = Message::orderBy('created_at')
            ->latest()
            ->take(50)
            ->get()
            ->sortBy('created_at')
            ->values();
    }

    public function sendMessage(): void
    {
        $this->validate();

        Message::create([
            'user_id'   => Auth::id(),
            'user_name' => $this->userName,
            'body'      => $this->messageText,
        ]);

        $this->messageText = '';

        $this->loadMessages();

        // Optionally scroll on frontend
        $this->dispatch('message-sent');
    }

    public function render()
    {
        return view('livewire.chat');
    }
}

