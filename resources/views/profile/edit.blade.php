@extends('layout.app')

@section('content')
    <div class="col-md-12 col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Profile Information</h4>
                <p class="text-subtitle text-muted">Update your account profile and password</p>
            </div>
            <div class="card-content">
                <div class="card-body">

                    {{-- Profile Update Form --}}
                    <form class="form form-horizontal" method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-3"><label>Name</label></div>
                                <div class="col-md-9">
                                    <div class="form-group has-icon-left">
                                        <div class="position-relative">
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name', $user->name) }}" required>
                                            <div class="form-control-icon"><i class="bi bi-person"></i></div>
                                        </div>
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3"><label>Email</label></div>
                                <div class="col-md-9">
                                    <div class="form-group has-icon-left">
                                        <div class="position-relative">
                                            <input type="email" name="email" class="form-control"
                                                value="{{ old('email', $user->email) }}" required>
                                            <div class="form-control-icon"><i class="bi bi-envelope"></i></div>
                                        </div>
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror

                                        {{-- Email Verification Notice --}}
                                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                            <div class="mt-2 text-warning">
                                                {{ __('Your email address is unverified.') }}
                                                <form id="send-verification" method="post"
                                                    action="{{ route('verification.send') }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-link p-0 align-baseline">
                                                        {{ __('Click here to re-send the verification email.') }}
                                                    </button>
                                                </form>
                                            </div>
                                            @if (session('status') === 'verification-link-sent')
                                                <p class="text-success small mt-1">
                                                    {{ __('A new verification link has been sent to your email address.') }}
                                                </p>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary me-1 mb-1">
                                        <i class="bi bi-person-check"></i> Update Profile
                                    </button>
                                </div>

                                @if (session('status') === 'profile-updated')
                                    <div class="col-12 mt-2">
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="bi bi-check-circle-fill"></i>
                                            Profile updated successfully!
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>

                    <hr class="my-4">

                    {{-- Password Update Form --}}
                    <h5 class="mb-3">Change Password</h5>
                    <form class="form form-horizontal" method="POST" action="{{ route('profile.password.update') }}"
                        id="password-form">
                        @csrf
                        @method('PUT')

                        <div class="form-body">

                            {{-- Success Message --}}
                            @if (session('status') === 'password-updated')
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="bi bi-check-circle-fill"></i>
                                            {{ session('message', 'Password updated successfully!') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Error Messages --}}
                            @if ($errors->any())
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <strong>Please fix the following errors:</strong>
                                            <ul class="mb-0 mt-2">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                {{-- New Password --}}
                                <div class="col-md-3 mt-3">
                                    <label for="password">New Password <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9 mt-3">
                                    <div class="form-group has-icon-left">
                                        <div class="position-relative">
                                            <input type="password" id="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                autocomplete="new-password" required minlength="8"
                                                placeholder="Enter new password (min 8 characters)">
                                            <div class="form-control-icon"><i class="bi bi-key"></i></div>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Minimum 8 characters required</small>
                                    </div>
                                </div>

                                {{-- Confirm Password --}}
                                <div class="col-md-3 mt-3">
                                    <label for="password_confirmation">Confirm Password <span
                                            class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9 mt-3">
                                    <div class="form-group has-icon-left">
                                        <div class="position-relative">
                                            <input type="password" id="password_confirmation"
                                                name="password_confirmation"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                autocomplete="new-password" required
                                                placeholder="Confirm your new password">
                                            <div class="form-control-icon"><i class="bi bi-shield-lock"></i></div>
                                        </div>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Submit Button --}}
                                <div class="col-12 d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary me-1 mb-1" id="update-password-btn">
                                        <i class="bi bi-key"></i> Update Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Password form validation
        document.getElementById('password-form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const submitBtn = document.getElementById('update-password-btn');

            // Client-side validation
            if (password !== confirmation) {
                alert('Password confirmation does not match!');
                e.preventDefault();
                return false;
            }

            if (password.length < 8) {
                alert('New password must be at least 8 characters!');
                e.preventDefault();
                return false;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Updating...';

            // Re-enable button after 3 seconds in case of error
            setTimeout(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-key"></i> Update Password';
            }, 3000);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    if (alert && alert.classList.contains('show')) {
                        alert.classList.remove('show');
                        alert.classList.add('fade');
                        setTimeout(function() {
                            if (alert.parentNode) {
                                alert.parentNode.removeChild(alert);
                            }
                        }, 150);
                    }
                }, 5000);
            });
        });
    </script>
@endsection
