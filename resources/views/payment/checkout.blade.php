@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="mb-4 fw-bold text-center">{{ __('Complete Your Payment') }}</h2>
            
            <form action="{{ route('payment.process') }}" method="POST" id="paymentForm">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold"><i class="fas fa-wallet me-2 text-primary"></i>{{ __('Select Payment Method') }}</h5>
                            </div>
                            <div class="card-body">
                                @error('payment_method')
                                    <div class="alert alert-danger py-2">{{ $message }}</div>
                                @enderror

                                <div class="payment-methods">
                                    
                                    <div class="form-check p-3 border rounded mb-2 method-container">
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input mt-0" type="radio" name="payment_method" id="cc" value="credit_card" required>
                                            <label class="form-check-label d-flex justify-content-between align-items-center w-100 ps-3" for="cc" role="button">
                                                <span class="fw-medium">Credit / Debit Card</span>
                                                <div class="text-muted">
                                                    <i class="fab fa-cc-visa fa-lg me-1"></i>
                                                    <i class="fab fa-cc-mastercard fa-lg"></i>
                                                </div>
                                            </label>
                                        </div>
                                        
                                        <div id="cc_details" class="payment-details mt-3 pt-3 border-top d-none">
                                            <div class="mb-3">
                                                <label class="form-label small text-muted">Card Number</label>
                                                <input type="text" class="form-control" placeholder="0000 0000 0000 0000">
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <label class="form-label small text-muted">Expiry</label>
                                                    <input type="text" class="form-control" placeholder="MM/YY">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small text-muted">CVC</label>
                                                    <input type="text" class="form-control" placeholder="123">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-check p-3 border rounded mb-2 method-container">
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input mt-0" type="radio" name="payment_method" id="bank" value="bank_transfer">
                                            <label class="form-check-label d-flex justify-content-between align-items-center w-100 ps-3" for="bank" role="button">
                                                <span class="fw-medium">Bank Transfer (Virtual Account)</span>
                                                <i class="fas fa-university text-muted"></i>
                                            </label>
                                        </div>

                                        <div id="bank_details" class="payment-details mt-3 pt-3 border-top d-none">
                                            <div class="alert alert-info mb-0">
                                                <p class="mb-1 small text-muted">Please transfer the exact amount to:</p>
                                                <h5 class="fw-bold text-primary mb-1">BCA Virtual Account</h5>
                                                <div class="d-flex align-items-center justify-content-between bg-white p-2 border rounded">
                                                    <span class="fs-5 fw-bold font-monospace" id="vaNumber">880123456789</span>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('vaNumber')">
                                                        <i class="far fa-copy">Copy</i>
                                                    </button>
                                                </div>
                                                <small class="d-block mt-2 text-muted fst-italic">Payment will be verified automatically.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-check p-3 border rounded method-container">
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input mt-0" type="radio" name="payment_method" id="ewallet" value="ewallet">
                                            <label class="form-check-label d-flex justify-content-between align-items-center w-100 ps-3" for="ewallet" role="button">
                                                <span class="fw-medium">QRIS (GoPay / OVO / Dana)</span>
                                                <i class="fas fa-qrcode text-muted"></i>
                                            </label>
                                        </div>

                                        <div id="ewallet_details" class="payment-details mt-3 pt-3 border-top text-center d-none">
                                            <p class="mb-2 small text-muted">Scan this QR code with your payment app:</p>
                                            <div class="bg-white p-3 d-inline-block border rounded shadow-sm">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=ExamplePaymentData" alt="QR Code" class="img-fluid">
                                            </div>
                                            <div class="mt-2 text-muted small">
                                                <i class="fas fa-spinner fa-spin me-1"></i> Waiting for payment...
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card shadow-lg border-primary">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0 fw-bold">{{ __('Order Summary') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Plan</span>
                                    <span class="fw-bold">Premium Monthly</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                                    <span class="text-muted">Price</span>
                                    <span>Rp 20.000</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">{{ __('Total') }}</h5>
                                    <h3 class="fw-bold text-primary">Rp 20.000</h3>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm">
                                    <i class="fas fa-lock me-2"></i> {{ __('Pay Now') }}
                                </button>
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i> {{ __('Secure Transaction') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('pricing') }}" class="text-decoration-none text-muted small">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('Cancel') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    
    function togglePaymentDetails() {
        document.querySelectorAll('.payment-details').forEach(el => {
            el.classList.add('d-none');
        });
        
        document.querySelectorAll('.method-container').forEach(el => {
            el.classList.remove('border-primary', 'bg-light');
        });

        const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
        
        if (checkedRadio) {
            const detailId = checkedRadio.id + '_details';
            const detailElement = document.getElementById(detailId);
            if (detailElement) {
                detailElement.classList.remove('d-none');
            }
            
            const container = checkedRadio.closest('.method-container');
            if (container) {
                container.classList.add('border-primary', 'bg-light');
            }
        }
    }

    paymentRadios.forEach(radio => {
        radio.addEventListener('change', togglePaymentDetails);
    });

    togglePaymentDetails();
});

function copyToClipboard(elementId) {
    const text = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert('Virtual Account number copied!');
    });
}
</script>
@endsection