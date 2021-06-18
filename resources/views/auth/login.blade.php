@extends('layouts.menu')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group col-md-4">
        <label for="email">{{ __('E-Mail Address') }}</label>
        <input type="email" size="20" class="form-control @error('email') is-invalid @enderror" id="email" name="email"  value="{{ old('email') }}" autofocus>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
    </div>
    <div class="form-group col-md-4">
        <label for="password">{{ __('Password') }}</label>
        <input type="password" size="20" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
    </div>

    <div class="form-group col-md-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
                {{ __('Remember Me') }}
            </label>
        </div>
    </div>

    <div class="form-group col-md-4">
            <button type="submit" class="btn btn-primary">
                {{ __('Login') }}
            </button>

            @if (Route::has('password.request'))
                <a class="btn btn-link" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            @endif
        <p></p><p><small>Only authorized members may use the database.</small></p>
    </div>
</form>
@endsection
