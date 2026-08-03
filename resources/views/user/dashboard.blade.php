@extends('layouts.user')

@section('title', 'Dashboard Keuangan')

@section('content')
<div class="space-y-6">
    <div>
        <p class="text-sm font-semibold text-blue-600">Akses laporan read-only</p>
        <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-800">Dashboard Keuangan</h1>
        <p class="mt-2 text-sm text-slate-400">{{ $summary['period_label'] }}</p>
    </div>

    @include('partials.summary-cards')
    @include('partials.latest-transactions')
</div>
@endsection
