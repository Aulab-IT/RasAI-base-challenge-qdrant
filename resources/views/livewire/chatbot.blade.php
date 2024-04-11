<div class="chat-container">
    <div class="chat-header">
        <p class="fs-4">{{ $chat?->title }}</p>
    </div>
    <div class="chat-box">
        @forelse ($chatMessages as $chatMessage)
            <div class="chat-message {{ $chatMessage->role == 'user' ? 'sent' : '' }}">
                <div class="chat-message-avatar">
                    <img src="https://via.placeholder.com/40" alt="Avatar">
                </div>
                <div class="">
                    @if($chatMessage->is_image_content)
                        <img width="512" src="{{ $chatMessage->content }}" alt="Image">
                    @else
                        <p>{{ $chatMessage->content }}</p>
                    @endif
                    @if ($chatMessage->audio_path)
                        <audio controls src="{{ Storage::url($chatMessage->audio_path) }}"></audio>
                    @endif
                </div>
            </div>
        @empty
            <div class="chat-message">
                <div class="">
                    <p>Send a message to start a conversation</p>
                </div>
            </div>
        @endforelse

        <div>
            <p wire:stream="generateResponse">{{ $openAiResponse }}</p>
        </div>
    </div>
    <form wire:submit="ask" class="chat-input align-items-center">
        <button type="button" wire:click="toggleImageMode" class="btn btn-outline-primary">
            @if($imageMode)
                <i class="bi bi-card-text"></i>
            @else
                <i class="bi bi-image"></i>
            @endif
        </button>
        <div class="flex-grow-1">
            <div id="inputWrapper" class="w-100 @if($audioState != 'idle') d-none @endif ">
                <input class="messageInput" wire:model.live="currentMessage" type="text">
                @error('currentMessage') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div id="audioWrapper" class="w-100 d-flex justify-content-between align-items-center @if($audioState != 'completed') d-none @endif">
                <button type="button" id="recordDeleteBtn" class="btn btn-danger">
                    <i class="bi bi-trash"></i>
                </button>
                <div id="playerWrapper" class="flex-grow-1 text-center" wire:ignore>
                    
                </div>
            </div>
        </div>
        <button id="audioRecorderBtn" type="button" class="btn btn-primary @if(!empty($currentMessage) || $audioState != 'idle') d-none @endif ">
            <i class="bi bi-mic"></i>
        </button>
        <button type="button" id="recordStopBtn" class="btn btn-outline-danger @if(!empty($currentMessage) || $audioState != 'recording') d-none @endif ">
            <i class="bi bi-stop-fill"></i>
        </button>
        @if ($audioState == 'completed' || !empty($currentMessage))
        <button type="submit" class="btn btn-primary messageBtn">
            <i class="bi bi-send"></i>
        </button>
        @endif
    </form>
</div>

@script
<script>
const audioRecorderBtn = document.querySelector('#audioRecorderBtn');
const recordDeleteBtn = document.querySelector('#recordDeleteBtn');
const recordStopBtn = document.querySelector('#recordStopBtn');


let recorder = {
    mediaRecorder: null,
    audioChunks: [],
    start: function() {
        this.updateState('recording');
        this.handleStartRecording();
    },
    stop: function() {
        this.mediaRecorder.stop();
    },

    delete: function() {
        this.mediaRecorder = null;
        this.updateState('idle');
    },

    updateState: function(state) {
        $wire.audioState = state;
        $wire.$refresh();
    },

    handleStartRecording: function() {
        this.audioChunks = [];

        navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {
            this.mediaRecorder = new MediaRecorder(stream);
            this.mediaRecorder.start();

            this.mediaRecorder.addEventListener("dataavailable", event => {
                this.audioChunks.push(event.data);
            });

            this.mediaRecorder.addEventListener("stop", () => {
                this.handleRecordingStop();
            });
        });
    },

    handleRecordingStop: function() {
        let file = new File(this.audioChunks, 'audio.mp3', { type: 'audio/mp3' });

        $wire.upload('audioMessage', file, (uploadedFilename) => {
            this.updateState('completed');
            this.generateAudio();
        }, () => {
            console.log('error on upload file');
        })
    },

    generateAudio: function() {
        const audioBlob = new Blob(this.audioChunks , { type: 'audio/mp3' });
        const audioUrl = URL.createObjectURL(audioBlob);
        const audio = new Audio(audioUrl);

        const playerWrapper = document.querySelector('#playerWrapper');

        audio.controls = true;
        playerWrapper.innerHTML = '';
        playerWrapper.appendChild(audio);
    }
    
    // CHALLENGE: Implement pause and resume methods
    // pause : function(){
    //     console.log('pause recording');
    //     recorder.mediaRecorder.pause();
    //     $wire.audioState = 'paused';
    // },

    // resume : function(){
    //     console.log('resume recording');
    //     recorder.mediaRecorder.resume();
    //     $wire.audioState = 'recording';
    // }
}

audioRecorderBtn.addEventListener('click', () => recorder.start());
recordDeleteBtn.addEventListener('click', () => recorder.delete() );
recordStopBtn.addEventListener('click', () => recorder.stop());

</script>
@endscript