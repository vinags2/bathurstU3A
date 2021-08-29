@extends('layouts.menu')

@section('content')
<script src="{{ asset('dist/utilities.js') }}"> </script>
<script src="{{ asset('dist/ajax.js') }}"> </script>
<script src="{{ asset('dist/newmember.js') }}"> </script>

    <div class="container">
        @include('partials.commonUI.pageHeading', ['pageHeading' => 'Edit Contact Details'])
        @include('partials.commonUI.showSuccessOrErrors')
        @if (!$showDetails)
            @include('partials.search.person')
        @else
        <form id="mainForm" method="post" action="{{ route('person.store') }}">
            @csrf
            <table class="table-sm" id="details">
               
                <tr><td id="nonMemberHeading" colspan="2"><b>
                    Person's details</b><td></tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="firstName">First Name:</label></td>
                    <td>
                        <input type="hidden" id="id" name="id" value="{{ old('id', $person['id']) }}"/>
                        <input type="text" size="40" id="firstName" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name',$person['first_name']) }}"
                        @if ($state == 'update existing member')
                            readonly="readonly"
                        @endif
                        />
                        @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="lastName">Last Name:</label></td>
                    <td>
                        <input type="text" id="lastName" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name', $person['last_name']) }}"
                        @if ($state == 'update existing member')
                            readonly="readonly"
                        @endif
                        />
                        @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        @php
                            $closeMatchingNames = request()->session()->pull('closeMatchingNames', null);
                        @endphp
                        @if (!empty($closeMatchingNames))
                            {{-- <label for="item" class="mt-1">Report for </label> --}}
                            <input type="hidden" id="closeMatchingNamesShown" name="closeMatchingNamesShown" value="true"/>
                            <select id="item" name="closeMatchingNames" class="form-control custom-select" size="3">
                                <option value="-1" disabled="disabled">If the name is in the list, please select it...</option>
                                @foreach ($closeMatchingNames as $name)
                                    <option value="{{ $name['id'] }}">{{ $name['name'] }}</option>
                                @endforeach
                            </select>
                            <br>
                            <input class="form-check-input ml-3"  id="confirm_name" type="checkbox" name="confirm_name" value="true">
                            <label class="form-check-label ml-5" for="confirm_name">accept the name as entered</label><BR>
                            <!-- <input class="form-check-input ml-3"  id="confirm_name" type="radio" name="confirm_name" value="false"> -->
                            <!-- <label class="form-check-label ml-5" for="confirm_name">re-enrol/edit the selected person</label><BR> -->
                        @endif
                        <span id="acceptorreenrolerror" style="display:none" class="invalid-feedback" role="alert">
                            <strong>You must make a choice: accept or re-enrol the member</strong>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="phone">Phone:</label></td>
                    <td>
                        <input type="text" id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $person['phone']) }}" autofocus="autofocus"/>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="mobile">Mobile:</label></td>
                    <td>
                        <input type="text" id="mobile" class="form-control @error('mobile') is-invalid @enderror" name="mobile" value="{{ old('mobile', $person['mobile']) }}"/>
                        @error('mobile')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="email">Email:</label></td>
                    <td>
                        <input type="text" id="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $person['email']) }}"/>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr><td></td><td></td></tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="line_1">Postal Address:</label></td>
                    <td>
                        <input type="text" id="address_input" class="form-control @error('line_1') is-invalid @enderror" name="line_1" value="{{ old('line_1', $postal_address['line_1']) }}"/>
                        <input type="hidden" id="address_url" value="{{ url('addresssearch') }}"/>
                        @error('line_1')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                            <select id="address_matches"  size="10" style="display:none" name="matches" class="form-control custom-select" onchange="displayNames(ajaxAddress)">
                            </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="text" id="line_2" class="form-control @error('line_2') is-invalid @enderror" name="line_2" value="{{ old('line_2', $postal_address['line_2']) }}"/>
                        @error('line_2')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="suburb">Suburb/Town/Locality:</label></td>
                    <td>
                        <input type="text" id="suburb" class="form-control @error('suburb') is-invalid @enderror" name="suburb" value="{{ old('suburb', $postal_address['suburb']) }}"/>
                        @error('suburb')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="postcode">Postcode:</label></td>
                    <td>
                        <input type="text" id="postcode" class="form-control @error('postcode') is-invalid @enderror" name="postcode" value="{{ old('postcode', $postal_address['postcode']) }}"/>
                        @error('postcode')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
               <tr>
                    <td><label class="col-xs-3 col-form-label mr-2" for="notes">Notes:</label></td>
                    <td>
                        <input type="text" maxlength="100" id="comment" class="form-control @error('notes') is-invalid @enderror" name="comment" value="{{ old('comment', $person['comment']) }}"/>
                        @error('notes')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr><td></td><td>[Max characters = 100]</td></tr>
                <!-- <tr><td colspan="2"><b>Actions</b></td></tr> -->
                <tr class="nonMember"
                    @if (!$member)
                       style="display:none"
                    @endif
                >
                    <td colspan="2" class="pt-4">
                        <button type="submit" name="save" value="true" class="btn btn-primary btn-sm">Save</button>
                        <button type="button" onclick=cancelForm("{{ route('person.edit') }}") class="btn btn-primary btn-sm">Back</button>
                   </td>
                </tr>
                <tr class="member"
                    @if ($member)
                       style="display:none"
                    @endif
                >
                    <td colspan="2">
                        <button type="submit" name="save" value="true" class="btn btn-primary btn-sm">Save</button>
                        <button type="button" onclick=cancelForm("{{ route('person.edit') }}") class="btn btn-primary btn-sm">Back</button>
                        <!-- <input class="form-check-input ml-3" type="radio" name="actions2" value="6" checked>
                        <label class="form-check-label ml-5" for="actions">Save the changes to the contact/non-member</label><BR> -->
                    </td>
                </tr>
                <!-- <tr>
                    <td colspan="2" class="pt-4" >
                        <button type="submit" name="save" class="btn btn-primary btn-sm">Save</button>
                        <button type="button" onclick=cancelForm("{{ route('person.edit') }}") class="btn btn-primary btn-sm">Back</button>
                        <button type="reset" class="btn btn-primary btn-sm">Reset</button>
                    </td> -->
                <!-- </tr> -->
            </table>
            <input type="hidden" id="memberId" value="{{ $person->id }}" name="memberId" />
            <input type="hidden" id="state" value="{{ $state }}" name="state" />
            <input type="hidden" id="contactDetailsOnly" value="true" name="contactDetailsOnly" />
        </form>
        @endif
    </div>
@endsection
