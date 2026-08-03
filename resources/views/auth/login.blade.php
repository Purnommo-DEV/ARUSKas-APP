<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk · ARUSKas</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @include('partials.pwa-meta')
    @vite(['resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50">
    <main class="grid min-h-screen lg:grid-cols-2">
        <section class="relative hidden overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-emerald-500 p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -left-20 -top-20 size-80 rounded-full border border-white/10 bg-white/5"></div>
            <div class="absolute -bottom-32 -right-20 size-96 rounded-full border border-white/10 bg-white/5"></div>
            <div class="relative flex items-center gap-3">
                <span class="grid size-12 place-items-center rounded-2xl bg-white text-xl font-black text-blue-700 shadow-xl">A</span>
                <span class="text-2xl font-extrabold">ARUSKas</span>
            </div>
            <div class="relative max-w-lg">
                <span class="mb-5 inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-widest">Sistem Keuangan Kajian</span>
                <h1 class="text-5xl font-black leading-tight">Catat dengan mudah.<br>Laporkan dengan amanah.</h1>
                <p class="mt-6 max-w-md text-base leading-7 text-blue-100">Satu ruang sederhana untuk membantu pengurus menjaga arus kas kajian tetap tertib dan transparan.</p>
            </div>
            <p class="relative text-xs text-blue-100">© {{ now()->year }} ARUSKas</p>
        </section>

        <section class="flex items-center justify-center p-5 sm:p-10">
            <div class="w-full max-w-md">
                <div class="mb-10 flex items-center gap-3 lg:hidden">
                    <span class="grid size-11 place-items-center rounded-2xl bg-blue-600 text-lg font-black text-white">A</span>
                    <span class="text-xl font-extrabold text-slate-800">ARUS<span class="text-blue-600">Kas</span></span>
                </div>
                <div class="surface-card p-6 sm:p-8">
                    <p class="text-sm font-bold text-blue-600">Selamat datang</p>
                    <h2 class="mt-1 text-3xl font-black tracking-tight text-slate-800">Masuk ke akun</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-400">Gunakan email dan kata sandi yang telah terdaftar.</p>

                    <form id="login-form" action="{{ route('login.store') }}" method="POST" class="mt-8 space-y-5">
                        @csrf
                        <div>
                            <label class="form-label" for="email">Email</label>
                            <input id="email" name="email" type="email" autocomplete="email" class="input" placeholder="nama@email.com" autofocus>
                            <p class="form-error" data-error-for="email"></p>
                        </div>
                        <div>
                            <label class="form-label" for="password">Kata Sandi</label>
                            <input id="password" name="password" type="password" autocomplete="current-password" class="input" placeholder="••••••••">
                            <p class="form-error" data-error-for="password"></p>
                        </div>
                        <label class="flex cursor-pointer items-center gap-3 text-sm text-slate-500">
                            <input type="hidden" name="remember" value="0">
                            <input type="checkbox" name="remember" value="1" class="checkbox checkbox-sm border-gray-300 checked:border-blue-600 checked:bg-blue-600">
                            Ingat saya di perangkat ini
                        </label>
                        <button type="submit" class="btn btn-primary h-12 w-full">Masuk ke ARUSKas</button>
                    </form>
                </div>
                <p class="mt-6 text-center text-xs leading-5 text-slate-400">Hubungi administrator kajian bila Anda mengalami kendala akses.</p>
            </div>
        </section>
    </main>
</body>
</html>
