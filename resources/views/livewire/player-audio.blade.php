<div>
    <button id="{{ 'player' . $id }}" class="btn btn-outline-accent rounded-pill">
        <i class="bi bi-play me-1"></i>
        <i class="bi bi-soundwave"></i>
    </button>
    <audio id="{{ 'audio' . $id }}" src="{{ $path }}"></audio>
</div>

@script
<script>
    let id = {{ $id }};
    let playBtn = document.querySelector(`#player${id}`);
    let audio = document.querySelector(`#audio${id}`);


    playBtn.addEventListener('click', () => {
        audio.play();
    });


</script>
@endscript
