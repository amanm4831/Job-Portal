@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('admin.sidebar')
            </div>
            <div class="col-lg-9">
                @include('front.message')
                <div class="card border-0 shadow mb-4">
                    <form method="POST" action="{{ route('account.updateProfile') }}" name="userForm" id="userForm">
                        @csrf
                        @method('PUT')
                        <div class="card-body p-4">
                            <h3 class="fs-4 mb-1">Edit User Profile</h3>
                            <div class="mb-4">
                                <label for="name" class="mb-2">Name*</label>
                                <input type="text" name="name" id="name" placeholder="Enter Name"
                                    class="form-control" value="{{ $user->name }}">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="email" class="mb-2">Email*</label>
                                <input type="text" name="email" id="email" value="{{ $user->email }}"
                                    placeholder="Enter Email" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="designation" class="mb-2">Designation</label>
                                <input type="text" name="designation" id="designation"
                                    value="{{ $user->designation }}" placeholder="Designation" class="form-control">
                            </div>
                            <div class="mb-4">
                                <label for="mobile" class="mb-2">Mobile</label>
                                <input type="text" name="mobile" id="mobile" value="{{ $user->mobile }}"
                                    placeholder="Mobile" class="form-control">
                            </div>
                        </div>
                        <div class="card-footer p-4">
                            <input type="submit" class="btn btn-primary" value="Update" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('customjs')
<script type="text/javascript">
    $('#userForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route('admin.users.update', $user->id) }}',
            type: 'PUT',
            dataType: 'json',
            data: $('#userForm').serialize(),
            success: function(response) {
                if (response.status == true) {
                    // Clear previous error messages
                    $(".form-control").removeClass('is-invalid');
                    $("p").removeClass('invalid-feedback').html('');

                    // Redirect on success
                    window.location.href = "{{ route('admin.users.list') }}";
                } else {
                    // Display errors
                    var errors = response.errors;
                    if (errors.name) {
                        $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback')
                            .html(errors.name);
                    } else {
                        $("#name").removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('');
                    }

                    if (errors.email) {
                        $("#email").addClass('is-invalid').siblings('p').addClass(
                            'invalid-feedback').html(errors.email);
                    } else {
                        $("#email").removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('');
                    }
                }
            }
        });
    });
</script>
@endsection
