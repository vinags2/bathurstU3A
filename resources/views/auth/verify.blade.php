@extends('layouts.menu')

@section('content')

<div class="container">
    <div class="row mt-1">
        <div>
            <h4>Email verification</h4>
            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
            @endif

            <p>{{ __('An email has been sent to you asking you to verify your email address.') }}</p>
            <p>{{ __('You may not login to the Bathurst U3A Database until you have verified your email address.') }}</p>
            <p>{{ __('If you did not receive the email') }}, <a href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.</p>
        </div>
    </div>
</div>

@endsection
