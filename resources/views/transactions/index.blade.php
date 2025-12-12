@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold fs-2">{{ __('dashboard.transaction_history') }}</h2>

{{-- FILTERING FORM (Wrapped in a Card for Better UI) --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light fw-bold">{{ __('transactions.filter_transactions') }}</div>
    <div class="card-body">
        <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end">
            
            {{-- SEARCH BAR INPUT --}}
            <div class="col-md-6 col-lg-2">
                <label for="search" class="form-label text-muted small">{{ __('transactions.search_name') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                       placeholder="{{ __('transactions.search_placeholder') }}" 
                       value="{{ request('search') }}">
            </div>
            
            {{-- NEW: WALLET FILTER DROPDOWN --}}
            <div class="col-md-6 col-lg-2">
                <label for="account" class="form-label text-muted small">{{ __('dashboard.wallets') }}</label>
                <select name="account" id="account" class="form-select">
                    <option value="">{{ __('transactions.all_wallets') }}</option>
                    {{-- Assumes $accounts is passed from the controller --}}
                    @foreach ($accounts as $account)
                        <option value="{{ $account->account_id }}" 
                                {{ request('account') == $account->account_id ? 'selected' : '' }}>
                            {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- TYPE FILTER DROPDOWN --}}
            <div class="col-md-6 col-lg-2">
                <label for="type" class="form-label text-muted small">{{ __('dashboard.type_col') }}</label>
                <select name="type" id="type" class="form-select">
                    <option value="">{{ __('transactions.all_types') }}</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>{{ __('dashboard.income') }}</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>{{ __('dashboard.expense') }}</option>
                </select>
            </div>
            
            {{-- CATEGORY FILTER DROPDOWN --}}
            <div class="col-md-6 col-lg-2">
                <label for="category" class="form-label text-muted small">{{ __('dashboard.select_category') }}</label>
                <select name="category" id="category" class="form-select">
                    <option value="">{{ __('transactions.all_categories') }}</option>
                    
                    @php 
                        // Ensure $categories is a collection before grouping
                        $groupedCategories = isset($categories) ? $categories->groupBy('type') : collect(); 
                    @endphp

                    @if ($groupedCategories->has('income'))
                        <optgroup label="{{ __('dashboard.income') }} ({{ __('transactions.all_incomes') }})">
                            @foreach ($groupedCategories['income'] as $cat)
                                <option value="{{ $cat->category_id }}" 
                                        {{ request('category') == $cat->category_id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif

                    @if ($groupedCategories->has('expense'))
                        <optgroup label="{{ __('dashboard.expense') }} ({{ __('transactions.all_expenses') }})">
                            @foreach ($groupedCategories['expense'] as $cat)
                                <option value="{{ $cat->category_id }}" 
                                        {{ request('category') == $cat->category_id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
            </div>

            {{-- Month Filter Dropdown --}}
            <div class="col-md-6 col-lg-2">
                <label for="month" class="form-label text-muted small">{{ __('transactions.filter_month') }}</label>
                <select name="month" id="month" class="form-select">
                    <option value="">{{ __('transactions.all_months') }}</option>
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->monthName }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Year Filter Dropdown --}}
            <div class="col-md-6 col-lg-2">
                <label for="year" class="form-label text-muted small">{{ __('transactions.filter_year') }}</label>
                <select name="year" id="year" class="form-select">
                    <option value="">{{ __('transactions.all_years') }}</option>
                    @php $currentYear = date('Y'); @endphp
                    @for ($y = $currentYear; $y >= $currentYear - 5; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            {{-- Buttons (Combined space) --}}
            <div class="col-12 d-flex gap-2 pt-2">
                
                {{-- Apply Filter Button --}}
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-1"></i> {{ __('transactions.apply_filter') }}
                </button>
                
                {{-- Reset Filter Button --}}
                @if (request()->has('month') || request()->has('year') || request()->has('category') || request()->has('search') || request()->has('type') || request()->has('account'))
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-1"></i> {{ __('transactions.reset_filter') }}
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- TRANSACTION TABLE --}}
<div class="table-responsive">
    <table class="table table-hover table-bordered shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>{{ __('dashboard.date') }}</th>
                <th>{{ __('dashboard.name') }}</th>
                <th>{{ __('dashboard.type_col') }}</th>
                <th class="text-end">{{ __('dashboard.amount_col') }}</th>
                
                {{-- PREMIUM GATE: Only show Budget column header for Premium users --}}
                @if (Auth::user()->isPremium())
                    <th>{{ __('dashboard.budget_if_expense') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $t)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($t->transaction_date)->format('d M Y') }}</td>
                    <td>{{ $t->name }}</td>
                    <td>
                        <span class="badge rounded-pill text-uppercase 
                            {{ $t->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                            {{ __('dashboard.' . $t->type) }}
                        </span>
                    </td>
                    {{-- Color code amount and align right --}}
                    <td class="text-end fw-bold text-{{ $t->type === 'income' ? 'success' : 'danger' }}">
                        Rp {{ number_format($t->amount) }}
                    </td>
                    
                    {{-- PREMIUM GATE: Only show Budget column data for Premium users --}}
                    @if (Auth::user()->isPremium())
                        <td>
                            @if($t->type === 'expense')
                                {{-- Use the budget_name field retrieved from the Controller's LEFT JOIN --}}
                                {{ $t->budget_name ? $t->budget_name . ' ' . __('budget.budget') : '-' }}
                            @else
                                -
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    {{-- Adjust colspan based on whether the budget column is visible (4 or 5 columns) --}}
                    <td colspan="{{ Auth::user()->isPremium() ? 5 : 4 }}" class="text-center py-3 text-muted">
                        {{ __('transactions.no_transactions') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="">
    {{ $transactions->appends(request()->all())->links('pagination::bootstrap-5') }}
</div>

@endsection