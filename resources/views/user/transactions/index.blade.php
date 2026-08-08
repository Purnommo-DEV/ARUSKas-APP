@extends('layouts.user')

@section('title', 'Transaksi')

@section('content')
@php($months = config('finance.months'))
<div id="transactions-module" data-read-only="true"
     data-data-url="{{ route('user.transactions.data') }}"
     class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-400">Ledger transaksi read-only</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-800">Daftar Transaksi</h1>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="transaction-month-filter" class="form-label">Filter Bulan</label>
                <select id="transaction-month-filter" class="select select-sm min-w-36" aria-label="Filter bulan">
                    @foreach($months as $number => $label)<option value="{{ $number }}" @selected($number === now()->month)>{{ $label }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="transaction-year-filter" class="form-label">Filter Tahun</label>
                <select id="transaction-year-filter" class="select select-sm min-w-28" aria-label="Filter tahun">
                    @foreach($years as $item)<option value="{{ $item }}" @selected($item === now()->year)>{{ $item }}</option>@endforeach
                </select>
            </div>
        </div>
    </div>
    @include('admin.transactions._table', ['readOnly' => true])
    @include('partials.proof-modal')
</div>
@endsection
