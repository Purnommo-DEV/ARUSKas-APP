<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem pengelolaan keuangan kajian ARUSKas.">
    <meta property="og:title" content="@yield('title', 'Dashboard') · ARUSKas">
    <meta property="og:description" content="Sistem pengelolaan keuangan kajian ARUSKas.">
    <meta name="twitter:card" content="summary">
    <title>@yield('title', 'Dashboard') · ARUSKas</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @include('partials.pwa-meta')
    @vite(['resources/js/app.js'])
</head>
<body class="min-h-screen">
    <aside class="desktop-sidebar fixed inset-y-0 left-0 z-50 hidden w-72 flex-col border-r border-gray-200 bg-white lg:flex">
        <div class="flex h-20 items-center border-b border-gray-100 px-6">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('icons/icon-192.png') }}" alt="ARUSKas" class="size-10 rounded-2xl shadow-sm">
                <span>
                    <span class="block text-lg font-extrabold tracking-tight text-slate-800">ARUS<span class="text-blue-600">Kas</span></span>
                    <span class="block text-[10px] font-bold uppercase tracking-[.2em] text-slate-400">Keuangan Kajian</span>
                </span>
            </a>
        </div>

        <nav class="flex-1 space-y-1.5 overflow-y-auto px-4 py-6">
            <p class="mb-3 px-3 text-[10px] font-extrabold uppercase tracking-[.2em] text-slate-400">Menu Utama</p>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                @include('partials.icon', ['name' => 'dashboard'])
                Dashboard
            </a>
            <a href="{{ route('admin.transactions.index') }}" class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                @include('partials.icon', ['name' => 'transaction'])
                Transaksi
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                @include('partials.icon', ['name' => 'report'])
                Laporan
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                @include('partials.icon', ['name' => 'category'])
                Master Kategori
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                @include('partials.icon', ['name' => 'users'])
                User
            </a>

            <p class="mb-3 mt-8 px-3 text-[10px] font-extrabold uppercase tracking-[.2em] text-slate-400">Sistem</p>
            <a href="{{ route('admin.settings.edit') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                @include('partials.icon', ['name' => 'settings'])
                Pengaturan
            </a>
        </nav>

        <div class="border-t border-gray-100 p-4">
            <a href="{{ route('admin.profile') }}" class="mb-3 flex items-center gap-3 rounded-2xl bg-slate-50 p-3 transition hover:bg-blue-50">
                <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-blue-100 font-extrabold text-blue-700">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-bold text-slate-700">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
                </div>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="nav-link w-full text-red-500 hover:bg-red-50 hover:text-red-600">
                    @include('partials.icon', ['name' => 'logout'])
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="min-h-screen lg:pl-72">
        <header class="sticky top-0 z-30 flex h-16 items-center border-b border-gray-200 bg-white/90 px-4 backdrop-blur-xl sm:px-6 lg:h-20 lg:px-8">
            <div class="min-w-0 flex-1">
                <h1 class="truncate text-lg font-extrabold text-slate-800 sm:text-xl">@yield('title', 'Dashboard')</h1>
                <p class="hidden text-xs text-slate-400 sm:block">Kelola arus kas kajian dengan transparan dan tertib.</p>
            </div>
            <div class="hidden items-center gap-2 rounded-xl border border-gray-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-500 sm:flex">
                <span class="size-2 rounded-full bg-emerald-500"></span>
                {{ now()->locale('id')->translatedFormat('d F Y') }}
            </div>
        </header>

        <main class="p-4 pb-[calc(6rem+env(safe-area-inset-bottom))] sm:p-6 sm:pb-[calc(6rem+env(safe-area-inset-bottom))] lg:p-8">
            @yield('content')
        </main>
    </div>

    <nav class="mobile-bottom-nav lg:hidden" aria-label="Navigasi utama">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">@include('partials.icon', ['name' => 'dashboard'])<span>Dashboard</span></a>
        <a href="{{ route('admin.transactions.index') }}" class="{{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">@include('partials.icon', ['name' => 'transaction'])<span>Transaksi</span></a>
        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">@include('partials.icon', ['name' => 'report'])<span>Laporan</span></a>
        <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">@include('partials.icon', ['name' => 'settings'])<span>Pengaturan</span></a>
        <a href="{{ route('admin.profile') }}" class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}">@include('partials.icon', ['name' => 'profile'])<span>Profil</span></a>
    </nav>
</body>
</html>
