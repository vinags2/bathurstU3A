@if (session('success'))
    <div class="container">
        <table id="successMessage" class="table-sm alert-success ml-5"><tr><td>
            {{ session('success') }}
        </td></tr><tr><td></td></tr></table>
    </div>
@elseif ($errors->any())
    <div class="container">
        <table class="table-sm alert-danger ml-5"><tr><td>
            Please fix the errors below...
        </td></tr><tr><td></td></tr>
        @foreach (array_unique($errors->all()) as $error)
            <tr class="alert-danger"><td>{{ $error }}</td></tr>
        @endforeach
    </table>
    </div>
@endif