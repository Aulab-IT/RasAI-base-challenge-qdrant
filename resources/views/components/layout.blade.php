<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>

    @vite(['resources/css/app.css' , 'resources/js/app.js'])
  </head>
  <body data-bs-theme="dark">
    <div class="d-flex">
        <x-sidebar/>
        <main class="w-100 p-4">
            {{ $slot }}
        </main>
    </div>
  </body>
</html>