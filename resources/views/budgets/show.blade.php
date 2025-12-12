@extends('layouts.app')

@section('content')

{{-- Check for Premium Status (Security UX - although this page is already middleware protected) --}}
@if (!Auth::user()->isPremium())
    <div class="alert alert-danger" role="alert">
        {{ __('messages.premium_required_budgets') }}
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary mt-3">{{ __('budget.back') }}</a>
    
@else

    @php
        $usedAmount = $budget->expenses->sum('amount');
        $limitAmount = $budget->amount;
        $remainingAmount = $limitAmount - $usedAmount;
        $isOverBudget = $remainingAmount < 0;
        $percentage = ($usedAmount / max(1, $limitAmount)) * 100;
        $progressClass = $percentage > 100 ? 'bg-danger' : ($percentage > 75 ? 'bg-warning' : 'bg-success');
    @endphp

    <h2 class="mb-4 fw-bold fs-2">{{ __('budget.budget') }}: {{ $budget->name }}</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white fw-bold">{{ __('budget.summary') }}</div>
        <div class="card-body">
            
            {{-- Progress Bar for Quick Status Check --}}
            <h5 class="fw-bold mb-3">
                {{ __('budget.status') }}: 
                <span class="text-{{ $isOverBudget ? 'danger' : 'success' }}">
                    {{ $isOverBudget ? __('budget.over_budget') : __('budget.in_budget') }}
                </span>
            </h5>
            
            <div class="progress mb-4" style="height: 25px;">
                <div class="progress-bar {{ $progressClass }} fw-bold" role="progressbar" 
                     style="width: {{ min(100, $percentage) }}%" 
                     aria-valuenow="{{ $percentage }}" 
                     aria-valuemin="0" aria-valuemax="100">
                    {{ round($percentage) }}% Used
                </div>
            </div>

            {{-- Budget Summary Cards --}}
            <div class="row g-3">
                
                {{-- Limit --}}
                <div class="col-md-4">
                    <div class="card border-primary h-100">
                        <div class="card-body py-2">
                            <h6 class="card-title text-primary text-uppercase small">{{ __('budget.limit') }}</h6>
                            <p class="card-text fs-5 fw-bold">Rp {{ number_format($limitAmount) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Used --}}
                <div class="col-md-4">
                    <div class="card border-warning h-100">
                        <div class="card-body py-2">
                            <h6 class="card-title text-warning text-uppercase small">{{ __('budget.used') }}</h6>
                            <p class="card-text fs-5 fw-bold">Rp {{ number_format($usedAmount) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Remaining / Status --}}
                <div class="col-md-4">
                    <div class="card h-100 border-{{ $isOverBudget ? 'danger' : 'success' }}">
                        <div class="card-body py-2">
                            <h6 class="card-title text-{{ $isOverBudget ? 'danger' : 'success' }} text-uppercase small">
                                {{ $isOverBudget ? __('budget.over_budget') : __('budget.remaining') }}
                            </h6>
                            <p class="card-text fs-5 fw-bold">Rp {{ number_format(abs($remainingAmount)) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <h4 class="mt-4 fw-bold">{{ __('budget.transactions') }}</h4>

    @if($budget->expenses->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover table-bordered shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>{{ __('dashboard.date') }}</th>
                        <th>{{ __('dashboard.name') }}</th>
                        <th class="text-end">{{ __('dashboard.amount_col') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($budget->expenses as $expense)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($expense->transaction_date)->format('d M Y') }}</td>
                            <td>{{ $expense->name }}</td>
                            <td class="text-end fw-bold text-danger">Rp {{ number_format($expense->amount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info shadow-sm mt-3">
            <p class="mb-0">{{ __('budget.no_budget_transactions') }}</p>
        </div>
    @endif

    <a href="{{ url()->previous() }}" class="btn btn-secondary mt-4">{{ __('budget.back') }}</a>

@endif
@endsection