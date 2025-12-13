@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold fs-2">{{ __('dashboard.your_wallets') }}</h2>

<div class="row row-cols-1 row-cols-md-3 g-4">
    @forelse ($accounts as $account)
        @php
            $balance = $account->calculated_balance ?? 0;
            $balanceClass = $balance >= 0 ? 'text-success' : 'text-danger';
        @endphp
        
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title fw-bold text-primary">{{ $account->name }}</h5>
                        <i class="fas fa-wallet fa-2x text-muted opacity-50"></i>
                    </div>
                    
                    <hr class="my-2">
                    
                    <h6 class="card-subtitle mb-2 text-muted">{{ __('dashboard.current_balance') }}</h6>
                    <p class="card-text fs-3 fw-bolder {{ $balanceClass }}">
                        Rp {{ number_format(abs($balance)) }}
                    </p>
                    
                    {{-- Button to view transaction history for this account --}}
                    <a href="{{ route('accounts.transactions', $account->account_id) }}" 
                       class="btn btn-outline-primary mt-auto">
                       <i class="fas fa-list-alt me-2"></i> {{ __('dashboard.view_history') }}
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info shadow-sm">
                {{ __('dashboard.no_wallets_found') }}
            </div>
        </div>
    @endforelse
</div>

@endsection