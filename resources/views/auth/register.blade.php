@extends('layouts.menu')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="form-group col-md-4">
        <label for="name">{{ __('Name') }}</label>
        <input type="text" size="20" class="form-control @error('name') is-invalid @enderror" id="name" name="name"  value="{{ old('name') }}" required autocomplete="name" autofocus>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
    </div>
    <div class="form-group col-md-4">
        <label for="email">{{ __('E-Mail Address') }}</label>
        <input type="email" size="20" class="form-control @error('email') is-invalid @enderror" id="email" name="email"  value="{{ old('email') }}">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <p>This is the email you provided to us when enrolling</p>
    </div>
    <div class="form-group col-md-4">
        <label for="password">{{ __('Password') }}</label>
        <input type="password" size="20" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
    </div>
    <div class="form-group col-md-4">
        <label for="password-confirm">{{ __('Confirm Password') }}</label>
        <input type="password" size="20" class="form-control" id="password-confirm" name="password_confirmation" required autocomplete="new-password">
    </div>

    <div class="form-group col-md-4">
            <button type="submit" class="btn btn-primary">
                {{ __('Register') }}
            </button>
    </div>
</form>
@endsection
