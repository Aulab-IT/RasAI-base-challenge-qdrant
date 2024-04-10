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

        <div>
            <p wire:stream="generateResponse">{{ $openAiResponse }}</p>
        </div>
    </div>
    <form wire:submit="ask" class="chat-input align-items-center">
        <div class="flex-grow-1" wire:ignore>
            <div id="inputWrapper" class="w-100">
                <input class="messageInput" wire:model="message" type="text">
                {{-- <input type="file" name="audio" wire:model="audioMessage"> --}}
                @error('message') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div id="audioWrapper" class="w-100 d-flex justify-content-between d-none align-items-center">
                <button type="button" id="recordDeleteBtn" class="btn btn-danger">
                    <i class="bi bi-trash"></i>
                </button>
                <div id="playerWrapper" class="flex-grow-1 text-center">
                    
                </div>
                <button type="button" id="recordStopBtn" class="btn btn-outline-danger">
                    <i class="bi bi-stop-fill"></i>
                </button>
            </div>
        </div>

        <button id="audioRecorderBtn" type="button" class="btn btn-primary">
            <i class="bi bi-mic"></i>
        </button>
        <button type="submit" class="btn btn-primary messageBtn">
            <i class="bi bi-send"></i>
        </button>
    </form>
</div>

@script
<script>
    const audioRecorderBtn = document.querySelector('#audioRecorderBtn');

if( audioRecorderBtn ){
    const inputWrapper = document.querySelector('#inputWrapper');
    const audioWrapper = document.querySelector('#audioWrapper');
    const recordDeleteBtn = document.querySelector('#recordDeleteBtn');
    const recordStopBtn = document.querySelector('#recordStopBtn');
    const playerWrapper = document.querySelector('#playerWrapper');
    const audioMessage = document.querySelector('#audioMessage');

    let recorder = {
        mediaRecorder : null,
        state: 'idle',
        start : function() {
            console.log('start recording');
            // Update the recorder state 
            recorder.state = 'recording';

            // Start the recording
            navigator.mediaDevices.getUserMedia({ audio: true })
            .then(stream => {
                
                recorder.mediaRecorder = new MediaRecorder(stream);
                recorder.mediaRecorder.start();
                
                const audioChunks = [];

                recorder.mediaRecorder.addEventListener("dataavailable", event => {
                    audioChunks.push(event.data);
                });

                recorder.mediaRecorder.addEventListener("stop", () => {
                    const audioBlob = new Blob(audioChunks , { type: 'audio/mp3' });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    
                    const audio = new Audio(audioUrl);
                    audio.controls = true;
                    playerWrapper.innerHTML = '';
                    playerWrapper.appendChild(audio);

                    let file = new File(audioChunks, 'audio.mp3', { type: 'audio/mp3' });

                    // let fileContainer = new DataTransfer();

                    // fileContainer.items.add(file);
                    // audioMessage.files = fileContainer.files;
                    
                    $wire.upload('audioMessage', file, (uploadedFilename) => {
                        // Success callback...
                        console.log('aweeeee')
                        console.log(uploadedFilename);
                    }, () => {
                        console.log('error');
                        // Error callback...
                    }, (event) => {
                        // Progress callback...
                        // event.detail.progress contains a number between 1 and 100 as the upload progresses
                    }, () => {
                        // Cancelled callback...
                    })
                });
            });

            recorder.updateDOMState();            
        },

        pause : function(){
            console.log('pause recording');
            recorder.mediaRecorder.pause();
            recorder.state = 'paused';
            recorder.updateDOMState();
        },

        resume : function(){
            console.log('resume recording');
            recorder.mediaRecorder.resume();
            recorder.state = 'recording';
            recorder.updateDOMState();

        },

        stop : function(){
            console.log('stop recording');
            console.log(recorder.mediaRecorder);
            recorder.mediaRecorder.stop();
            recorder.state = 'completed';
            recorder.updateDOMState();
        },

        delete : function(){
            console.log('delete recording');
            recorder.mediaRecorder = null;
            recorder.state = 'idle';
            // Hide the record button & show the stop button 
            inputWrapper.classList.remove('d-none');
            audioWrapper.classList.add('d-none');
            audioRecorderBtn.classList.remove('d-none');
            recorder.updateDOMState();
        },

        updateDOMState : function(){
            switch( recorder.state ){
                case 'idle':
                    inputWrapper.classList.remove('d-none');
                    audioWrapper.classList.add('d-none');
                    audioRecorderBtn.innerHTML = '<i class="bi bi-mic"></i>';
                    audioRecorderBtn.classList.remove('d-none');
                    recordStopBtn.classList.remove('d-none');
                    break;
                case 'recording':
                    inputWrapper.classList.add('d-none');
                    audioWrapper.classList.remove('d-none');
                    audioRecorderBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
                    playerWrapper.innerHTML = 'Recording...';;
                    break;
                case 'paused':
                    audioRecorderBtn.innerHTML = '<i class="bi bi-mic"></i>';
                    playerWrapper.innerHTML = 'Paused...';
                    break;
                case 'completed':
                    audioRecorderBtn.classList.add('d-none');
                    recordStopBtn.classList.add('d-none');

                    break;
            }
        },


    }

    audioRecorderBtn.addEventListener('click', () => {
        if( !recorder.mediaRecorder ){
            recorder.start();
        } else {
            switch( recorder.state ){
                case 'recording':
                    recorder.pause();
                    break;
                case 'paused':
                    recorder.resume();
                    break;
            }
        }
    });
    recordDeleteBtn.addEventListener('click', recorder.delete );
    recordStopBtn.addEventListener('click', recorder.stop );
}
</script>
@endscript