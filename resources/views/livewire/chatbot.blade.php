<div class="chat-container">
    <div class="chat-header my-3 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between pe-2">
        <h2 class="fs-4 text-truncate">{{ $chat?->title }}</h2>

        {{-- switch option --}}

        <div class="checkbox-wrapper-35 mt-3 mt-lg-0">
            <input value="private" name="switch" id="switch" type="checkbox" class="switch" wire:model.live="imageMode">
            <label for="switch">
              <span class="switch-x-text">Generate </span>
              <span class="switch-x-toggletext">
                <span class="switch-x-unchecked"><span class="switch-x-hiddenlabel">Unchecked: </span>Text</span>
                <span class="switch-x-checked"><span class="switch-x-hiddenlabel">Checked: </span>Image</span>
              </span>
            </label>
        </div>
       

    </div>
    <div class="chat-box">
        @forelse ($chatMessages as $chatMessage)
            <div class="chat-message {{ $chatMessage->role == 'user' ? 'sent' : '' }}">
                <div class="chat-message-avatar">
                    @if($chatMessage->role == 'assistant')
                    <img src="/LOGO-DarkBG.png" alt="Avatar">
                    @else 
                    <img src="https://github.com/mdo.png" alt="" class="">
                    @endif
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
                    <p class="fs-3">What do you need today?</p>
                </div>
            </div>
        @endforelse

        <div>
            <p wire:stream="generateResponse">{{ $openAiResponse }}</p>
        </div>
    </div>
    <form wire:submit="ask" class="chat-input align-items-center">
        {{-- <div class="form-check form-switch">
            <input wire:model.live="imageMode" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
            <label class="form-check-label" for="flexSwitchCheckDefault">{{ var_export($imageMode) }}</label>
        </div> --}}
        {{-- <div class="toggle-cont">
            <input class="toggle-input" id="toggle" name="toggle" type="checkbox" />
            <label class="toggle-label" for="toggle">
              <div class="cont-label-play">
                <span class="label-play"></span>
              </div>
            </label>
          </div> --}}
        
          
          
{{--     
        <button type="button" wire:click="toggleImageMode" class="btn">
            @if($imageMode)
                <i class="bi bi-card-text"></i>
            @else
                <i class="bi bi-image"></i>
            @endif
        </button> --}}
        <div class="flex-grow-1">
            <div id="inputWrapper" class="w-100 @if($audioState != 'idle') d-none @endif ">
                <input class="messageInput" placeholder="Send a message to start the conversation..." wire:model.live="currentMessage" type="text">
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
        <button id="audioRecorderBtn" type="button" class="btn btn-primary-custom @if(!empty($currentMessage) || $audioState != 'idle') d-none @endif ">
            <i class="bi bi-mic"></i>
        </button>
        <button type="button" id="recordStopBtn" class="btn btn-outline-danger @if(!empty($currentMessage) || $audioState != 'recording') d-none @endif ">
            <i class="bi bi-stop-fill"></i>
        </button>
        @if ($audioState == 'completed' || !empty($currentMessage))
        <button type="submit" class="btn messageBtn">
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
        $wire.audioMessage = null;
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