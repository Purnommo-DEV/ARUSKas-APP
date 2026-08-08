@extends('layouts.admin')

@section('title', 'Transaksi')

@section('content')
@php
    $months = config('finance.months');
@endphp
<div id="transactions-module"
     data-read-only="false"
     data-data-url="{{ route('admin.transactions.data') }}"
     data-store-url="{{ route('admin.transactions.store') }}"
     data-base-url="{{ url('/admin/transactions') }}"
     data-category-store-url="{{ route('admin.categories.store') }}"
     data-today="{{ now()->toDateString() }}"
     class="space-y-6">
    <div class="surface-card p-5">
        <div class="grid gap-4 sm:grid-cols-2 lg:max-w-xl">
            <div>
                <label for="transaction-month-filter" class="form-label">Filter Bulan</label>
                <select id="transaction-month-filter" class="select">
                    @foreach($months as $number => $label)
                        <option value="{{ $number }}" @selected($number === now()->month)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="transaction-year-filter" class="form-label">Filter Tahun</label>
                <select id="transaction-year-filter" class="select">
                    @foreach($years as $item)
                        <option value="{{ $item }}" @selected($item === now()->year)>{{ $item }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @include('admin.transactions._table')
    @include('admin.transactions._modal')
    @include('partials.proof-modal')
    <button type="button" class="mobile-fab lg:hidden" data-add-transaction aria-label="Tambah transaksi">+</button>
</div>
@endsection
