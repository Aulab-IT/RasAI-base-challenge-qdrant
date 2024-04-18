{{-- <div class="d-flex flex-column flex-shrink-0 bg-body-tertiary sidebar" style="width: 4.5rem;">
    <a href="/" class="d-block p-3 link-body-emphasis text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Icon-only">
      <img class="img-fluid" src="/logo.png" alt="">
      <span class="visually-hidden">Icon-only</span>
    </a>
    <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
      <li class="nav-item">
        <a href="#" class="nav-link fs-3 active py-3 border-bottom rounded-0" aria-current="page" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Home" data-bs-original-title="Home">
            <i class="bi bi-house"></i>
        </a>
      </li>
      <li>
        <a href="#" class="nav-link fs-3 py-3 border-bottom rounded-0" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Dashboard" data-bs-original-title="Dashboard">
            <i class="bi bi-speedometer2"></i>
        </a>
      </li>
    </ul>
    <div class="dropdown border-top">
      <a href="#" class="d-flex align-items-center justify-content-center p-3 link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://github.com/mdo.png" alt="mdo" width="24" height="24" class="rounded-circle">
      </a>
      <ul class="dropdown-menu text-small shadow">
        <li><a class="dropdown-item" href="#">New project...</a></li>
        <li><a class="dropdown-item" href="#">Settings</a></li>
        <li><a class="dropdown-item" href="#">Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#">Sign out</a></li>
      </ul>
    </div>
</div> --}}

<div class="d-flex align-items-center">
  <button class="btn d-md-none ms-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasResponsive" aria-controls="offcanvasResponsive">
    <i class="bi bi-list fs-1"></i>
  </button>
  <h1 class="mb-0 d-block d-md-none">RagsAI</h1>
</div>

<div class="offcanvas-md offcanvas-start" tabindex="-1" id="offcanvasResponsive" aria-labelledby="offcanvasResponsiveLabel">
  <div class="offcanvas-header">
    <a href="{{ route('chat.index') }}" class="d-flex align-items-center gap-3 mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
      <img class="logo" src="/RagsAI-LOGO.png" alt="">
      <h1 class="fs-3">RagsAI</h1>
    </a>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#offcanvasResponsive" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="d-flex flex-column flex-shrink-0 w-100 p-md-4 sidebar">
      <a href="{{ route('chat.index') }}" class="d-none d-md-flex align-items-center gap-3 mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
          <img class="logo" src="/RagsAI-LOGO.png" alt="">
          <h1 class="fs-3">RagsAI</h1>
      </a> 
      <hr>
      <ul class="nav nav-pills flex-column mb-auto">
        {{-- <li class="nav-item">
          <a href="{{ route('home') }}" class="nav-link link-body-emphasis {{ (Route::currentRouteName() == 'home') ? 'active' : ''}}" aria-current="page">
              <i class="bi bi-house"></i>
              Home
          </a>
        </li> --}}
        <li>
          <a 
            href="{{ route('chat.index') }}" 
            class="nav-link link-body-emphasis {{ (Route::currentRouteName() == 'chat.index') ? 'active' : ''}}"
          >
            <i class="bi bi-chat"></i>
            Chat
          </a>
        </li>
        <li>
          <a 
            href="{{ route('documents.index') }}" 
            class="nav-link link-body-emphasis {{ (Route::currentRouteName() == 'documents.index') ? 'active' : ''}}"
          >
            <i class="bi bi-file-earmark-break"></i>
            Documents
          </a>
        </li>
      </ul>
      <hr>
      <div class="dropdown">
        <a href="#" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
          <strong>Mario Rossi</strong>
        </a>
        <ul class="dropdown-menu text-small shadow">
          <li><a class="dropdown-item" href="#">Settings</a></li>
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="#">Sign out</a></li>
        </ul>
      </div>
  </div>
  </div>
</div>


