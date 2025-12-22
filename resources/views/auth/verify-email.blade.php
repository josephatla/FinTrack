<x-guest-layout>
    <div class="mb-4 small text-muted">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 alert alert-success small">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 d-flex justify-content-between align-items-center">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <button type="submit" class="btn btn-primary">
                    {{ __('Resend Verification Email') }}
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="btn btn-link text-decoration-none text-muted small">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>