<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul') | {{ config('laundry.nama') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="text-center mt-3 no-print">
        <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Cetak</button>
        <a href="{{ $kembali ?? url()->previous() }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
    @yield('content')
</body>
</html>
