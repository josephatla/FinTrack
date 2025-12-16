@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold fs-2">{{ __('dashboard.welcome', ['name' => Auth::user()->name]) }}</h2>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row mb-5">
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card shadow-sm border-success h-100">
            <div class="card-body">
                <h5 class="card-title text-success text-uppercase">{{ __('dashboard.total_income') }}</h5>
                <h3 class="card-text fw-bold">Rp {{ number_format($totalIncome) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card shadow-sm border-danger h-100">
            <div class="card-body">
                <h5 class="card-title text-danger text-uppercase">{{ __('dashboard.total_expense') }}</h5>
                <h3 class="card-text fw-bold">Rp {{ number_format($totalExpense) }}</h3>
            </div>
        </div>
    </div>
    @php $netBalance = $totalIncome - $totalExpense; @endphp
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card shadow-sm border-primary h-100">
            <div class="card-body">
                <h5 class="card-title text-primary text-uppercase">Net Balance</h5>
                <h3 class="card-text fw-bold">Rp {{ number_format($netBalance) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-6 mb-4">
        @if (Auth::user()->isPremium())
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white fw-bold">{{ __('dashboard.add_budget') }}</div>
                <div class="card-body">
                    <form action="{{ route('budgets.store') }}" method="POST" class="row g-3" id="budgetForm">
                        @csrf
                        <div class="col-12">
                            <input type="text" class="form-control" name="budget_name" placeholder="{{ __('dashboard.budget_name') }}" required value="{{ old('budget_name') }}">
                        </div>
                        <div class="col-12">
                            <input type="number" class="form-control" name="budget_amount" placeholder="{{ __('dashboard.amount') }}" required value="{{ old('budget_amount') }}">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.add_budget_btn') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="card shadow-lg bg-white h-100 d-flex flex-column justify-content-center border-primary">
                <div class="card-header bg-primary text-white fw-bold">{{ __('dashboard.add_budget') }}</div>
                <div class="card-body text-center">
                    <p class="fs-5 text-muted mb-3">
                        <i class="fas fa-lock me-2 text-danger"></i> {{ __('messages.premium_budget_cta') }}
                    </p>
                    <a href="{{ route('pricing') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-up me-1"></i> {{ __('messages.upgrade_now') }}
                    </a>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-6 mb-4">
        <div class="card shadow-lg">
            <div class="card-header bg-success text-white fw-bold">{{ __('dashboard.add_transaction') }}</div>
            <div class="card-body">
                <form action="{{ route('transactions.store') }}" method="POST" class="row g-3" id="transactionForm">
                    @csrf

                    <div class="col-12">
                        <select id="transactionType" name="type" class="form-select" required>
                            <option value="">{{ __('dashboard.type') }}</option>
                            <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>{{ __('dashboard.income') }}</option>
                            <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>{{ __('dashboard.expense') }}</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <select name="account_id" class="form-select" required>
                            <option value="">{{ __('dashboard.select_account') }}</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->account_id }}" {{ old('account_id') == $account->account_id ? 'selected' : '' }}>{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12" id="categorySelect" style="display: none;">
                        <select name="category_id" class="form-select" id="categoryOptions">
                            <option value="">{{ __('dashboard.select_category') }}</option>
                        </select>
                        
                        <div class="mt-2 text-end">
                            @if (Auth::user()->isPremium())
                                <button type="submit" 
                                        formaction="{{ route('session.draft') }}" 
                                        formnovalidate 
                                        class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-plus me-1"></i> {{ __('dashboard.add_category') ?? 'Add New Category' }}
                                </button>
                            @else
                                <small class="text-muted">
                                    {{ __('messages.premium_category_cta_short') ?? 'Upgrade to Premium to create new categories.' }}
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="col-12" id="budgetSelect" style="display: none;">
                        @if (Auth::user()->isPremium())
                            <select name="budget_id" class="form-select">
                                <option value="">{{ __('dashboard.select_budget') }}</option>
                                @foreach($budgets as $budget)
                                    <option value="{{ $budget->budget_id }}" {{ old('budget_id') == $budget->budget_id ? 'selected' : '' }}>{{ $budget->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="hidden" name="budget_id" value=""> 
                            <div class="alert alert-info py-2 m-0" role="alert">
                                {{ __('messages.premium_budget_tracker_note') }}
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <input type="text" class="form-control" name="transaction_name" placeholder="{{ __('dashboard.transaction_name') }}" required value="{{ old('transaction_name') }}">
                    </div>

                    <div class="col-6">
                        <input type="number" class="form-control" name="transaction_amount" placeholder="{{ __('dashboard.amount') }}" required value="{{ old('transaction_amount') }}">
                    </div>
                    <div class="col-6">
                        <input type="date" class="form-control" name="transaction_date" required value="{{ old('transaction_date') }}">
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-success">{{ __('dashboard.add_transaction_btn') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<h4 class="mt-4 mb-3 fw-bold">{{ __('dashboard.your_budgets') }}</h4>
<div class="row mb-5">
    @if (Auth::user()->isPremium() && count($budgets) > 0)
        @foreach ($budgets as $budget)
            @php
                $usedAmount = $budget->expenses->sum('amount');
                $limitAmount = $budget->amount;
                $percentage = ($usedAmount / max(1, $limitAmount)) * 100;
                $progressClass = $percentage > 100 ? 'bg-danger' : ($percentage > 75 ? 'bg-warning' : 'bg-success');
            @endphp
            <div class="col-sm-6 col-lg-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-primary">{{ $budget->name }}</h5>
                        <p class="card-text small text-muted">
                            {{ __('dashboard.limit') }}: Rp {{ number_format($limitAmount) }}
                        </p>
                        <div class="progress mb-2" style="height: 15px;">
                            <div class="progress-bar {{ $progressClass }}" role="progressbar" 
                                 style="width: {{ min(100, $percentage) }}%" 
                                 aria-valuenow="{{ $percentage }}" 
                                 aria-valuemin="0" aria-valuemax="100">
                                {{ round($percentage) }}%
                            </div>
                        </div>
                        <p class="card-text text-muted mt-auto">
                            {{ __('dashboard.used') }}: Rp {{ number_format($usedAmount) }}
                        </p>
                        <a href="{{ route('budgets.show', $budget->budget_id) }}" class="btn btn-outline-primary btn-sm mt-2">
                            {{ __('dashboard.details') }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="col-12">
            <div class="alert alert-secondary text-center">
                <p class="mb-1 fw-bold">{{ __('dashboard.no_budgets') }}</p>
                @if (!Auth::user()->isPremium())
                    <p class="mb-0 text-info small">{{ __('messages.premium_budget_check_list') }}</p>
                @endif
            </div>
        </div>
    @endif
</div>

<h4 class="mt-4 mb-3 fw-bold">{{ __('dashboard.latest_transactions') }}</h4>
<div class="table-responsive">
    <table class="table table-hover table-bordered shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>{{ __('dashboard.date') }}</th>
                <th>{{ __('dashboard.name') }}</th>
                <th>{{ __('dashboard.type_col') }}</th>
                <th>{{ __('dashboard.amount_col') }}</th>
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
                        <span class="badge rounded-pill text-uppercase {{ $t->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($t->type) }}
                        </span>
                    </td>
                    <td class="fw-bold text-{{ $t->type === 'income' ? 'success' : 'danger' }}">Rp {{ number_format($t->amount) }}</td>
                    @if (Auth::user()->isPremium())
                        <td>{{ $t->type === 'expense' ? ($t->budget->name ?? '-') : '-' }}</td> 
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ Auth::user()->isPremium() ? 5 : 4 }}" class="text-center text-muted py-3">
                        {{ __('dashboard.no_transactions') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('transactionType');
    const budgetSelect = document.getElementById('budgetSelect');
    const categorySelect = document.getElementById('categorySelect');
    const categoryOptions = document.getElementById('categoryOptions');

    const isPremium = {{ Auth::user()->isPremium() ? 'true' : 'false' }};
    const incomeCategories = @json($incomeCategories);
    const expenseCategories = @json($expenseCategories);
    
    const oldType = "{{ old('type') }}";
    const oldCategory = "{{ old('category_id') }}";
    const oldBudget = "{{ old('budget_id') }}";

    function updateForm(type, selectedCategoryId = null) {
        if(budgetSelect) budgetSelect.style.display = type === 'expense' ? 'block' : 'none';
        categorySelect.style.display = type ? 'block' : 'none';

        categoryOptions.innerHTML = '<option value="">{{ __('dashboard.select_category') }}</option>';

        const categories = type === 'income' ? incomeCategories : expenseCategories;
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.category_id;
            option.textContent = cat.name;
            if (selectedCategoryId && cat.category_id == selectedCategoryId) {
                option.selected = true;
            }
            categoryOptions.appendChild(option);
        });
    }

    typeSelect.addEventListener('change', function () {
        updateForm(this.value);
    });
    
    if (oldType) {
        typeSelect.value = oldType;
        updateForm(oldType, oldCategory);
        if (oldBudget && typeSelect.value === 'expense') {
            const budgetOption = document.querySelector(`select[name="budget_id"] option[value="${oldBudget}"]`);
            if (budgetOption) budgetOption.selected = true;
        }
    }

    const csrfToken = document.querySelector('input[name="_token"]').value;
    
    function autoSave(formId, formType) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('input', debounce(function() {
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => { data[key] = value });

            fetch('{{ route("session.autosave") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ form: formType, data: data })
            });
        }, 1000)); 
    }

    function debounce(func, timeout = 1000){
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => { func.apply(this, args); }, timeout);
        };
    }
    autoSave('budgetForm', 'budget');
    autoSave('transactionForm', 'transaction');
});
</script>

@endsection