@extends($layout)

@section('title', $title)

@section('content')
@php($months = config('finance.months'))
<div id="transactions-module"
     data-read-only="true"
     data-data-url="{{ $dataUrl }}"
     data-summary-url="{{ $summaryUrl }}"
     class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-blue-600">Pembukuan transparan</p>
            <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-800 sm:text-3xl">Laporan Keuangan</h2>
            <p class="mt-2 text-sm text-slate-400" data-summary="period">{{ $summary['period_label'] }}</p>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:w-auto">
            <div>
                <label for="transaction-month-filter" class="form-label">Filter Bulan</label>
                <select id="transaction-month-filter" class="select" aria-label="Filter bulan">
                    @foreach($months as $number => $label)
                        <option value="{{ $number }}" @selected($number === now()->month)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="transaction-year-filter" class="form-label">Filter Tahun</label>
                <select id="transaction-year-filter" class="select" aria-label="Filter tahun">
                    @foreach($years as $item)
                        <option value="{{ $item }}" @selected($item === now()->year)>{{ $item }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @include('partials.summary-cards')
    @include('admin.transactions._table', ['readOnly' => true])
    @include('partials.proof-modal')
</div>
@endsection
