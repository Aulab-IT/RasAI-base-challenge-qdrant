<div class="chatlist-container">
    <button 
        wire:click="newChat" 
        class="btn mb-3 btn-outline-accent w-100 text-start text-truncate"
    >
        New Chat
    </button>
    @forelse ($chats as $chat)
        <button 
            wire:click="selectChat({{ $chat->id }})" 
            class="btn mb-3 {{ $chat->id == $chat_id ? 'btn-primary-custom' : 'btn-outline-custom' }} w-100 text-start text-truncate"
        >
            {{ $chat->title }}
        </button>
    @empty
        
    @endforelse
</div>
