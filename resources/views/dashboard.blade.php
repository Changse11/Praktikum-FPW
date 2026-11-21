<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard - POS Barokah Mart</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-sm w-full max-w-md text-center">
        <h1 class="text-xl font-semibold">Dashboard</h1>
        <p class="text-gray-600 mt-2">
            Selamat datang, {{ auth()->user()->name }} ({{ auth()->user()->role }})
        </p>
        <form action="{{ route('logout') }}" method="POST" class="mt-6">
            @csrf
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Keluar</button>
        </form>
    </div>
</body>
</html>