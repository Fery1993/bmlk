<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — BMLK Keuangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 600:'#4f46e5', 700:'#4338ca', 800:'#3730a3', 900:'#312e81' }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-gradient-to-br from-primary-900 via-primary-800 to-indigo-900 flex items-center justify-center p-4">

<div class="w-full max-w-sm">
    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-2xl p-8">

        {{-- Logo --}}
        <div class="flex flex-col items-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-primary-700 flex items-center justify-center mb-3">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-800">BMLK Keuangan</h1>
            <p class="text-sm text-gray-400 mt-0.5">PT. Berkah Murni Lahan Kita</p>
        </div>

        {{-- Error --}}
        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent transition"
                       placeholder="admin@perusahaan.co.id">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Password</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent transition"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-primary-600">
                <label for="remember" class="text-xs text-gray-500">Ingat saya</label>
            </div>

            <button type="submit"
                    class="w-full bg-primary-700 hover:bg-primary-800 text-white font-medium py-2.5 rounded-lg text-sm transition">
                Masuk
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-6">
            Sistem Keuangan Internal &copy; {{ date('Y') }}
        </p>
    </div>
</div>

</body>
</html>
