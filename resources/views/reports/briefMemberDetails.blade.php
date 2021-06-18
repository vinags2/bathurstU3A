@extends('layouts.menu');
@section('content');

<div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Contact Details for '{{ $user->name }}])
        <!-- <div class="form-row">
            <h5><label class="mt-1 ml-5">Contact Details for {{ $user->name }}</label></h5>
        </div> -->
    
    <table class="table table-striped table-sm table-bordered table-fit">
        <tbody>
        @if (!empty($user->phone))
        <tr>
            <td>
                phone:
            </td>
            <td>
                {{ $user->phone }}
            </td>
        </tr>
        @endif
        @if (!empty($user->mobile))
        <tr>
            <td>
                mobile:
            </td>
            <td>
                {{ $user->mobile }}
            </td>
        </tr>
        @endif
        @if (!empty($user->email))
        <tr>
            <td>
                email:
            </td>
            <td>
                @include('partials.helpers.email',['email' => $user->email])
            </td>
        </tr>
        @endif
        </tbody>
    </table>
</div>
<br>
@endsection