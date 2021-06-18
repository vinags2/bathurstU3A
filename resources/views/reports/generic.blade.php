@extends('layouts.menu')
@section('content')
<div class="container">
    @include('partials.commonUI.pageHeading', ['pageHeading' => $pageHeading])
    <small>({{ $total }} records)</small>
    <table class="table table-striped table-sm table-bordered">
        @if (!empty($data))
            <thead>
                <tr>
                    @foreach ($data[0] as $heading => $cellData) 
                        <th> {{  $heading }} </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        @foreach ($row as $heading => $cellData) 
                        <td>
                            @if ($heading == 'address')
                                @include('partials.helpers.address',['address' => $row, 'href' => $attributes[$loop->parent->index][$heading]['href']])
                            @elseif ($heading == 'email')
                                @include('partials.helpers.email',['email' => $cellData, 'href' => $attributes[$loop->parent->index][$heading]['href']])
                            @elseif ($heading == 'last name')
                                @include('partials.helpers.member',['member' => $cellData, 'id' => $attributes[$loop->parent->index][$heading]['href']])
                            @elseif ($heading == 'venue')
                                @include('partials.helpers.venue',['venue' => $cellData, 'id' => $attributes[$loop->parent->index][$heading]['href']])
                            @elseif ($heading == 'facilitator')
                                @include('partials.helpers.member',['member' => $cellData, 'id' => $attributes[$loop->parent->index][$heading]['href']])
                            @elseif ($heading == 'course')
                                @include('partials.helpers.course',['course' => $cellData, 'id' => $attributes[$loop->parent->index][$heading]['href']])
                            @else
                                {{ $cellData }}
                            @endif
                        </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        @else
            <tr>
                <td></td>
                <td>
                    There are none.
                </td>
            </tr>
        @endif
    </table>
    @if (!empty($data))
            <table>
                <tr><td>
                    {{ $links }}
                </td><td>
                </td><td>
                    <ul class="pagination">
                        <li class="page-item">
                            <a class="page-link" target="_blank" href="{{ $pdf }}">PDF</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" target="_blank" href="{{ $csv }}">CSV</a>
                        </li>
                    </ul>
                </td>
                </tr>
            </table>
    @endif
</div>
<!-- <br> -->

@endsection