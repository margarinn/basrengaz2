<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Basreng AZ-2 - Camilan Basreng Premium</title>
    <meta name="description" content="Basreng AZ-2 - Camilan basreng premium dengan bumbu rempah asli dan daun jeruk segar.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @php
      $manifestPath = public_path('build/.vite/manifest.json');
      $manifest = file_exists($manifestPath)
        ? json_decode(file_get_contents($manifestPath), true)
        : null;
      $entry = $manifest['resources/js/src/main.ts'] ?? null;
    @endphp
    @if($entry && isset($entry['css']))
      @foreach($entry['css'] as $css)
        <link rel="stylesheet" href="/build/{{ $css }}">
      @endforeach
    @endif
  </head>
  <body>
    <div id="app"></div>
    @if($entry)
      <script type="module" src="/build/{{ $entry['file'] }}"></script>
    @else
      <script type="module" src="http://localhost:5173/resources/js/src/main.ts"></script>
    @endif
  </body>
</html>
