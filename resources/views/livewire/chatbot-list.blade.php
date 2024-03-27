<div class="chatlist-container">
    <button 
        wire:click="newChat" 
        class="btn mb-3 btn-outline-success w-100 text-start text-truncate"
    >
        New Chat
    </button>
    @forelse ($chats as $chat)
        <button 
            wire:click="selectChat({{ $chat->id }})" 
            class="btn mb-3 {{ $chat->id == $chat_id ? 'btn-primary' : 'btn-outline-primary' }} w-100 text-start text-truncate"
        >
            {{ $chat->title }}
        </button>
    @empty
        
    @endforelse
</div>
