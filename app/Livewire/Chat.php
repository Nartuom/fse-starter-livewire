<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads; 
class Chat extends Component
{
    use WithFileUploads;
    public string $userName = '';
    public $avatarImage;
    public string $messageText = '';
    public $chatMessages;

    protected $rules = [
        'userName'    => 'required|string|max:255',
        'messageText' => 'required|string|max:2000',
    ];

    public function mount(): void
    {
        if (Auth::check() && $this->userName === '') {
            $this->userName = Auth::user()->name ?? '';            
        }

        $this->loadMessages();
    }

    public function loadMessages(): void
    {
        $this->chatMessages = Message::with('user')
            ->orderBy('created_at')
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
        $this->dispatch('message-sent');
    }

    public function saveAvatar(): void
    {
         // If no file selected, just exit gracefully
        if (!$this->avatarImage) {
            session()->flash('status', 'No image selected.');
            return;
        }
        $this->validate([
            'avatarImage' => 'nullable|image|max:10240', 
        ]);
        
        $path = $this->avatarImage->store('avatarImages', 'public');
        $this->avatarImage->store('avatarImages', 'public');
        
        $user = Auth::user();
        $user->avatar_url = $path;
        $user->save();  
        $this->reset('avatarImage');

        session()->flash('status', 'Avatar updated.');
    }


    public function render()
    {
        return view('livewire.chat');
    }
}

