<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ojuju:wght@200..800&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">

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