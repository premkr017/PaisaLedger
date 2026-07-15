<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PaisaLedger' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight text-indigo-600">
                PaisaLedger
            </a>

            <nav class="flex items-center gap-4 text-sm font-medium">
                <a href="{{ route('dashboard') }}" class="text-slate-600 transition hover:text-indigo-600">Dashboard</a>
                <a href="{{ route('transactions.index') }}" class="text-slate-600 transition hover:text-indigo-600">Transactions</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-white transition hover:bg-slate-700">
                        Log out
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{ $slot }}
    </main>
</body>
</html>
