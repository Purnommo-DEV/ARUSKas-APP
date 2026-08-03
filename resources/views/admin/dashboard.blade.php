@extends('layouts.admin')

@section('title', 'Dashboard Keuangan')

@section('content')
<div class="space-y-6">
    <div>
        <p class="text-sm font-semibold text-slate-400">Ringkasan periode berjalan</p>
        <p class="mt-1 text-2xl font-black text-slate-800">{{ $summary['period_label'] }}</p>
    </div>

    @include('partials.summary-cards')
    @include('partials.latest-transactions')
</div>
@endsection
