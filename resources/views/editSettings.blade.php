@extends('layouts.menu')

@section('content')
    <script src="{{ asset('dist/utilities.js') }}"> </script>
    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Edit Settings for '.$currentYear])
        <script>
            function updateTotalNumberOfWeeks() {
                numberOfTermsCtl = document.getElementById('numberOfTerms');
                weeksInTermCtl = document.getElementById('weeksInTerm');
                totalNumberOfWeeksCtl = document.getElementById('totalNumberOfWeeks');
                totalNumberOfWeeksCtl.value = numberOfTermsCtl.value * weeksInTermCtl.value;
            }
        </script>
        <form id="mainForm" method="post" action="{{ $action }}">
            @csrf
            <table class="table-sm" id="details">

                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="rejoinStartDate">Memberships for next year begin:</label></td>
                    <td>
                        <input type="date" size="40" id="rejoinStartDate" class="form-control @error('rejoin_start_date') is-invalid @enderror" 
                            name="rejoin_start_date" value="{{ old('rejoin_start_date',date('Y-m-d', strtotime($settings['rejoin_start_date']))) }}"
                        />
                        @error('rejoin_start_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="pt-4" >
                        <input type="hidden" id="SettingsPage1" name="SettingsPage1" value="true"/>
                        <button type="submit" name="save" class="btn btn-primary btn-sm">Save</button>
                        <button type="reset" class="btn btn-primary btn-sm">Reset</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
@endsection
