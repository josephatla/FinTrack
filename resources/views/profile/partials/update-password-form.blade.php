<section>
    <header class="mb-4">
        <h2 class="h5 text-dark fw-bold">
            {{ __('dashboard.update_password') }}
        </h2>

        <p class="text-muted small">
            {{ __('dashboard.update_password_msg') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">{{ __('dashboard.current_password') }}</label>
            <input type="password" name="current_password" id="update_password_current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">{{ __('dashboard.new_password') }}</label>
            <input type="password" name="password" id="update_password_password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">{{ __('dashboard.confirm_password') }}</label>
            <input type="password" name="password_confirmation" id="update_password_password_confirmation" class="form-control" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">{{ __('dashboard.save') }}</button>

            @if (session('status') === 'password-updated')
                <span class="text-success fw-bold small fade-in">
                    <i class="bi bi-check-circle"></i> {{ __('dashboard.saved') }}
                </span>
            @endif
        </div>
    </form>
</section>