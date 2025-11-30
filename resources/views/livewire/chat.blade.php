<div class="space-y-4">

    <div
    class="h-full flex flex-col gap-4"
    x-data
    x-init="
        // Load saved username from localStorage on first load
        if (localStorage.getItem('chat_user_name')) {
            $wire.userName = localStorage.getItem('chat_user_name');
        }

        $wire.on('message-sent', () => {
            const container = document.getElementById('chat-messages');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    "
>
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight">Team Chat</h1>
        <p class="text-xs text-slate-500">
            Messages are shared for all logged-in users.
        </p>
    </div>

    {{-- Username input --}}
    <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3">
        <label class="text-sm font-medium text-slate-700">
            Display name
        </label>
        <input
            type="text"
            wire:model.live="userName"
            x-on:input="localStorage.setItem('chat_user_name', $event.target.value)"
            class="flex-1 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
            placeholder="How should we show your messages?"
        >
    </div>

    {{-- Chat panel --}}
    <div class="flex-1 bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden">
        <div
            id="chatMessages"
            class="flex-1 overflow-y-auto px-4 py-3 space-y-3"
            wire:poll.2s="loadMessages"
        >
            @forelse ($chatMessages as $message)
                <div class="flex flex-col text-sm">
                    <div class="flex items-baseline gap-2">
                        <span class="font-semibold text-slate-800">
                            {{ $message->user_name }}
                        </span>
                        <span class="text-[11px] uppercase tracking-wide text-slate-400">
                            {{ $message->created_at->format('H:i') }}
                        </span>
                    </div>
                    <div class="mt-1 rounded-lg bg-slate-50 px-3 py-2 text-slate-800">
                        {{ $message->body }}
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 p-1 align-middle">
                    No messages yet. Say hi 👋
                </p>
            @endforelse
        </div>

        {{-- Message form --}}
        <form
            wire:submit.prevent="sendMessage"
            class="border-t border-slate-200 px-4 py-3 flex items-end gap-3 bg-slate-50/60"
        >
            <textarea
                wire:model.live="messageText"
                rows="1"
                class="flex-1 resize-none rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2"
                placeholder="Type your message and press Enter to send..."
                x-on:keydown.enter.prevent="$wire.sendMessage()"
            ></textarea>

            <button
                type="submit"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 disabled:opacity-40"
            >
                Send
            </button>
        </form>
    </div>
</div>

    
</div>
