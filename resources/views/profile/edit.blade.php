@extends('layouts.app')

@section('content')
<div class="container py-4">
    <header class="mb-4">
        <h2 class="fw-bold">
            {{ __('dashboard.profile') }}
        </h2>
    </header>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection