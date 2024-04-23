<div>
    <button 
        wire:click="newChat" 
        class="btn mb-3 btn-outline-accent w-100 text-start text-truncate"
    >
        New Chat
    </button>
    <div class="chatlist-container">

        @forelse ($chats as $chat)
        <div 
            
            class="p-0 btn mb-3 btn-chat {{ $chat->id == $chat_id ? 'btn-primary-custom' : 'btn-outline-custom' }} w-100 "
            >
            <button class="btn titleBtn w-100 text-start text-truncate" wire:click="selectChat({{ $chat->id }})" >
                {{ $chat->title }}
            </button> 

            <button class="btn deleteBtn" wire:click="deleteChat({{ $chat->id }})">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        @empty
        
        @endforelse
    </div>
</div>
