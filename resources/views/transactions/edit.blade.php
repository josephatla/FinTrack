@extends('layouts.app')

@section('content')
<div class="container">
    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white fw-bold">
                    {{ __('dashboard.edit') }}
                </div>
                <div class="card-body">
                    <form action="{{ route('transactions.update', $transaction->getKey()) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" value="{{ $type }}">

                        <div class="mb-3">
                            <label class="form-label text-muted small">{{ __('dashboard.type_col') }}</label>
                            <input type="text" class="form-control" value="{{ ucfirst($type) }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.select_account') }}</label>
                            <select name="account_id" class="form-select" required>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->account_id }}" 
                                        {{ $transaction->account_id == $account->account_id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.select_category') }}</label>
                            <select name="category_id" class="form-select">
                                <option value="">{{ __('dashboard.select_category') }}</option>
                                @foreach($categories as $category)
                                    @if($category->type === $type)
                                        <option value="{{ $category->category_id }}" 
                                            {{ $transaction->category_id == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        @if($type === 'expense' && Auth::user()->isPremium())
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.select_budget') }}</label>
                                <select name="budget_id" class="form-select">
                                    <option value="">{{ __('dashboard.select_budget') }}</option>
                                    @foreach($budgets as $budget)
                                        <option value="{{ $budget->budget_id }}" 
                                            {{ $transaction->budget_id == $budget->budget_id ? 'selected' : '' }}>
                                            {{ $budget->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.transaction_name') }}</label>
                            <input type="text" class="form-control" name="transaction_name" 
                                   value="{{ old('transaction_name', $transaction->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.amount') }}</label>
                            <input type="number" class="form-control" name="transaction_amount" 
                                   value="{{ old('transaction_amount', $transaction->amount) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.date') }}</label>
                            <input type="date" class="form-control" name="transaction_date" 
                                   value="{{ old('transaction_date', $transaction->transaction_date) }}" required>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('transactions.index') }}" class="btn btn-secondary me-2">{{ __('dashboard.cancel') ?? 'Cancel' }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.save') ?? 'Save Changes' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection