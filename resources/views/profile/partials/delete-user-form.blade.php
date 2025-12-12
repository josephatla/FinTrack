<section>
    <header class="mb-4">
        <h2 class="h5 text-dark fw-bold">
            {{ __('dashboard.delete_account') }}
        </h2>

        <p class="text-muted small">
            {{ __('dashboard.delete_account_msg') }}
        </p>
    </header>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        {{ __('dashboard.delete_btn') }}
    </button>

    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">{{ __('dashboard.delete_account') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <h6 class="fw-bold text-danger">{{ __('dashboard.confirm_delete') }}</h6>
                    <p class="small text-muted">
                        {{ __('dashboard.delete_account_msg') }}
                    </p>

                    <div class="mt-3">
                        <label for="password" class="form-label visually-hidden">{{ __('dashboard.password') }}</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                            placeholder="{{ __('dashboard.password') }}"
                        >
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('dashboard.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-danger">
                        {{ __('dashboard.delete_btn') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>