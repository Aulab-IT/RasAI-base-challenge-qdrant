<div class="chat-container">
    <div class="chat-header">
        <p class="fs-4">{{ $chat?->title }}</p>
    </div>
    <div class="chat-box">
        {{-- <div class="chat-message sent">
            <div class="chat-message-avatar">
                <img src="https://via.placeholder.com/40" alt="Avatar">
            </div>
            <div class="">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vel libero nec libero lacinia.</p>
            </div>
        </div>
        <div class="chat-message">
            <div class="chat-message-avatar">
                <img src="https://via.placeholder.com/40" alt="Avatar">
            </div>
            <div class="">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vel libero nec libero lacinia.</p>
            </div>
        </div> --}}

        @forelse ($messages as $message)
            <div class="chat-message {{ $message->role == 'user' ? 'sent' : '' }}">
                <div class="chat-message-avatar">
                    <img src="https://via.placeholder.com/40" alt="Avatar">
                </div>
                <div class="">
                    <p>{{ $message->content }}</p>
                </div>
            </div>
        @empty
            <div class="chat-message">
                <div class="">
                    <p>Send a message to start a conversation</p>
                </div>
            </div>
        @endforelse
    </div>
    <form wire:submit="ask" class="chat-input">
        <div class="w-100">
            <input wire:model="message" type="text">
            @error('message') <span class="error">{{ $message }}</span> @enderror
        </div>
        <button>
            <i class="bi bi-send"></i>
        </button>
    </form>
</div>
