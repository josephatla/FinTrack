@extends('layouts.app')

@section('content')

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold">{{ __('pricing.title') }}</h1>
        <p class="lead text-muted">{{ __('pricing.subtitle') }}</p>
    </div>

    <div class="row row-cols-1 row-cols-md-2 g-4 justify-content-center">

        <div class="col">
            <div class="card h-100 shadow-sm border-secondary">
                <div class="card-header text-center py-3 bg-light">
                    <h4 class="my-0 fw-bold">{{ __('pricing.tier_free') }}</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    <h1 class="card-title pricing-card-title text-center">
                        {{ __('pricing.price_free') }}
                    </h1>
                    <ul class="list-unstyled mt-3 mb-4 text-start">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> {{ __('pricing.feature_unlimited_transactions') }}</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> {{ __('pricing.limit_five_accounts') }}</li>
                    </ul>
                    
                    @if (!Auth::user()->isPremium())
                        <button class="w-100 btn btn-lg btn-outline-secondary mt-auto" disabled>
                            {{ __('pricing.button_current') }}
                        </button>
                    @else
                        <a href="{{ route('dashboard') }}" class="w-100 btn btn-lg btn-outline-secondary mt-auto">
                            {{ __('pricing.button_dashboard') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 shadow-lg border-primary">
                <div class="card-header text-center py-3 bg-primary text-white">
                    <h4 class="my-0 fw-bold">{{ __('pricing.tier_premium') }}</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    <h1 class="card-title pricing-card-title text-center">
                        Rp 20.000<small class="text-muted fw-light">/{{ __('pricing.period_month') }}</small>
                    </h1>
                    <ul class="list-unstyled mt-3 mb-4 text-start">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> {{ __('pricing.feature_unlimited_transactions') }}</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> {{ __('pricing.feature_unlimited_accounts') }}</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> {{ __('pricing.feature_budgets') }}</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> {{ __('pricing.feature_custom_categories') }}</li>
                    </ul>
                    
                    @if (Auth::user()->isPremium())
                        <button class="w-100 btn btn-lg btn-primary mt-auto" disabled>
                            {{ __('pricing.button_active') }}
                        </button>
                    @else
                        <a href="{{ route('payment.checkout') }}" class="w-100 btn btn-lg btn-primary mt-auto">
                            {{ __('pricing.button_upgrade') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>
    
    <div class="text-center mt-5">
        <p class="text-muted small">
            {{ __('pricing.billing_note') }}
        </p>
    </div>
</div>

@endsection