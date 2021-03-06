<div class="container">
    @if (session('success'))
        <div class="d-flex justify-content-center row alert-success col-5">
            {{ session('success') }}
        </div>
    @elseif ($errors->any())
        <div class="d-flex justify-content-center row alert-danger col-5">
            Please fix the errors below...
            <ul>
                @foreach (array_unique($errors->all()) as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>