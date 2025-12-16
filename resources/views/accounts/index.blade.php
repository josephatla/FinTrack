@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold fs-2 mb-0">{{ __('dashboard.your_wallets') }}</h2>
        
        @php
            $isPremium = Auth::user()->isPremium();
            $count = $accounts->count();
            $limit = 5;
            $canAdd = $isPremium || $count < $limit;
        @endphp

        @if($canAdd)
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                <i class="fas fa-plus-circle me-1"></i> {{ __('dashboard.add_account') }}
            </button>
        @else
            <div class="text-end">
                <span class="badge bg-warning text-dark mb-1">{{ __('dashboard.limit_reached') }} ({{ $count }}/{{ $limit }})</span>
                <br>
                <a href="{{ route('pricing') }}" class="btn btn-sm btn-outline-primary">{{ __('messages.upgrade_now') }}</a>
            </div>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row row-cols-1 row-cols-md-3 g-4">
        @forelse ($accounts as $account)
            @php
                $balance = $account->calculated_balance ?? 0;
                $balanceClass = $balance >= 0 ? 'text-success' : 'text-danger';
            @endphp
            
            <div class="col">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-primary">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="card-title fw-bold text-dark mb-1">{{ $account->name }}</h5>
                                <span class="badge bg-light text-muted border small">{{ $account->type }}</span>
                            </div>
                            <i class="fas fa-wallet text-muted opacity-25 fa-2x"></i>
                        </div>
                        
                        <h6 class="card-subtitle mt-3 mb-2 text-muted small text-uppercase">{{ __('dashboard.current_balance') }}</h6>
                        <p class="card-text fs-3 fw-bolder {{ $balanceClass }} mb-4">
                            Rp {{ number_format(abs($balance)) }}
                        </p>
                        
                        <div class="mt-auto d-flex flex-column gap-2">
                            <a href="{{ route('accounts.transactions', $account->account_id) }}" class="btn btn-outline-primary w-100">
                               <i class="fas fa-list-alt me-2"></i> {{ __('dashboard.view_history') }}
                            </a>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary flex-grow-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editAccountModal{{ $account->account_id }}">
                                    <i class="fas fa-edit me-1"></i> {{ __('dashboard.edit') }}
                                </button>

                                <form action="{{ route('accounts.destroy', $account->account_id) }}" method="POST" class="flex-grow-1"
                                      onsubmit="return confirm('{{ __('messages.confirm_delete_account') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-trash me-1"></i> {{ __('dashboard.delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editAccountModal{{ $account->account_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('accounts.update', $account->account_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">{{ __('dashboard.edit_account') ?? 'Edit Wallet' }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('dashboard.name') }}</label>
                                    <input type="text" name="name" class="form-control" value="{{ $account->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('dashboard.type_col') }}</label>
                                    <select name="type" class="form-select" required>
                                        <option value="Bank" {{ $account->type == 'Bank' ? 'selected' : '' }}>Bank</option>
                                        <option value="Cash" {{ $account->type == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="E-Wallet" {{ $account->type == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                                        <option value="Investment" {{ $account->type == 'Investment' ? 'selected' : '' }}>Investment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-wallet fa-4x text-muted mb-3 opacity-25"></i>
                <p class="text-muted fs-5">{{ __('dashboard.no_wallets_found') }}</p>
            </div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="addAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('accounts.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">{{ __('dashboard.add_account') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('dashboard.name') }}</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. BCA, Mandiri, Pocket" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('dashboard.type_col') }}</label>
                        <select name="type" class="form-select" required>
                            <option value="" disabled selected>{{ __('dashboard.type') }}</option>
                            <option value="Bank">Bank</option>
                            <option value="Cash">Cash</option>
                            <option value="E-Wallet">E-Wallet</option>
                            <option value="Investment">Investment</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('dashboard.add_account_btn') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection