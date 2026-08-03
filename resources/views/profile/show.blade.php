@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : 'layouts.user')

@section('title', 'Profil')

@section('content')
<div class="mx-auto max-w-2xl">
    <div class="surface-card overflow-hidden">
        <div class="bg-gradient-to-br from-blue-700 to-emerald-500 px-6 py-8 text-white sm:px-8">
            <div class="grid size-16 place-items-center rounded-2xl bg-white/20 text-2xl font-black">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <h2 class="mt-4 text-2xl font-black">{{ auth()->user()->name }}</h2>
            <p class="mt-1 text-sm text-blue-100">{{ auth()->user()->hasRole('admin') ? 'Admin' : 'User' }}</p>
        </div>
        <dl class="divide-y divide-gray-100 p-5 sm:p-6">
            <div class="flex items-center justify-between gap-4 py-3 first:pt-0">
                <dt class="text-sm text-slate-400">Email</dt>
                <dd class="truncate text-right text-sm font-bold text-slate-700">{{ auth()->user()->email }}</dd>
            </div>
            <div class="flex items-center justify-between gap-4 py-3 last:pb-0">
                <dt class="text-sm text-slate-400">Akses</dt>
                <dd class="text-sm font-bold text-slate-700">{{ auth()->user()->hasRole('admin') ? 'Kelola pembukuan' : 'Lihat laporan keuangan' }}</dd>
            </div>
        </dl>
        <form action="{{ route('logout') }}" method="POST" class="border-t border-gray-100 p-5 sm:p-6">
            @csrf
            <button class="btn btn-outline w-full border-red-200 text-red-600 hover:border-red-600 hover:bg-red-600">Keluar dari akun</button>
        </form>
    </div>
</div>
@endsection
