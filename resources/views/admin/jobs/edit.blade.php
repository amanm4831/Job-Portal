@extends('front.layouts.app')

@section('main')
    <section class="section-5 bg-2">
        <div class="container py-5">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
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
                        <div class="card-body card-form">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="fs-4 mb-1">Edit Job</h3>
                                </div>
                                {{-- <div style="margin-top: -10px;">
                                <a href="{{route('account.createJob')}}" class="btn btn-primary">Post a Job</a>
                            </div> --}}

                            </div>
                            <form action="" method="POST" id="createJobForm" name="createJobForm">
                                @csrf <!-- CSRF token for security -->
                                <div>
                                    <div class="card-body card-form p-4">
                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <label for="title" class="mb-2">Title<span
                                                        class="req">*</span></label>
                                                <input value="{{ $job->title }}" type="text" placeholder="Job Title"
                                                    id="title" name="title" class="form-control" required>
                                                <p class="text-danger small"></p>
                                            </div>

                                            <div class="col-md-6  mb-4">
                                                <label for="category" class="mb-2">Category<span
                                                        class="req">*</span></label>
                                                <select name="category" id="category" class="form-control" required>
                                                    <option value="">Select a Category</option>
                                                    @foreach ($categories as $category)
                                                        <option {{ $job->category_id == $category->id ? 'selected' : '' }}
                                                            value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                                <p class="text-danger small"></p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <label for="jobType" class="mb-2">Job Type<span
                                                        class="req">*</span></label>
                                                <select name="jobType" id="jobType" class="form-select" required>
                                                    <option value="">Select Job Type</option>
                                                    @foreach ($jobTypes as $jobType)
                                                        <option {{ $job->job_types_id == $jobType->id ? 'selected' : '' }}
                                                            value="{{ $jobType->id }}">{{ $jobType->name }}</option>
                                                    @endforeach
                                                </select>
                                                <p class="text-danger small"></p>
                                            </div>

                                            <div class="col-md-6  mb-4">
                                                <label for="vacancy" class="mb-2">Vacancy<span
                                                        class="req">*</span></label>
                                                <input value="{{ $job->vacancy }}" type="number" min="1"
                                                    placeholder="Vacancy" id="vacancy" name="vacancy" class="form-control"
                                                    required>
                                                <p class="text-danger small"></p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="mb-4 col-md-6">
                                                <label for="salary" class="mb-2">Salary</label>
                                                <input value="{{ $job->salary }}" type="text" placeholder="Salary"
                                                    id="salary" name="salary" class="form-control">
                                            </div>

                                            <div class="mb-4 col-md-6">
                                                <label for="location" class="mb-2">Location<span
                                                        class="req">*</span></label>
                                                <input value="{{ $job->location }}" type="text" placeholder="Location"
                                                    id="location" name="location" class="form-control" required>
                                                <p class="text-danger small"></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-4 col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1"
                                                        id="isFeatured" name="isFeatured">
                                                    <label class="form-check-label" for="isFeatured">
                                                        Featured
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="mb-4 col-md-6">
                                                <div class="form-check-inline">
                                                    <input class="form-check-input" type="radio" value="1"
                                                        id="status-active" name="status">
                                                    <label class="form-check-label" for="status">
                                                        Active
                                                    </label>
                                                </div>
                                                <div class="form-check-inline">
                                                    <input class="form-check-input" type="radio" value="0"
                                                        id="status-block" name="status">
                                                    <label class="form-check-label" for="status">
                                                        Block
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="description" class="mb-2">Description<span
                                                    class="req">*</span></label>
                                            <textarea class="form-control" name="description" id="description" cols="5" rows="5"
                                                placeholder="Description" required>{{ $job->description }}</textarea>
                                            <p class="text-danger small"></p>
                                        </div>

                                        <div class="mb-4">
                                            <label for="benefits" class="mb-2">Benefits</label>
                                            <textarea class="form-control" name="benefits" id="benefits" cols="5" rows="5"
                                                placeholder="Benefits">{{ $job->benefits }}</textarea>
                                        </div>

                                        <div class="mb-4">
                                            <label for="responsibility" class="mb-2">Responsibility</label>
                                            <textarea class="form-control" name="responsibility" id="responsibility" cols="5" rows="5"
                                                placeholder="Responsibility">{{ $job->responsibility }}</textarea>
                                        </div>

                                        <div class="mb-4">
                                            <label for="qualifications" class="mb-2">Qualifications</label>
                                            <textarea class="form-control" name="qualifications" id="qualifications" cols="5" rows="5"
                                                placeholder="Qualifications">{{ $job->qualification }}</textarea>
                                        </div>

                                        <div class="mb-4">
                                            <label for="keywords" class="mb-2">Keywords</label>
                                            <input value="{{ $job->keywords }}" type="text" placeholder="Keywords"
                                                id="keywords" name="keywords" class="form-control">
                                        </div>

                                        <div class="mb-4">
                                            <label for="experience" class="mb-2">Experience<span
                                                    class="req">*</span></label>
                                            <select name="experience" id="experience" class="form-control" required>
                                                <option value="">Years of experience</option>
                                                <option value="1" {{ $job->experience == 1 ? 'selected' : '' }}>1 Year
                                                </option>
                                                <option value="2"{{ $job->experience == 2 ? 'selected' : '' }}>2 Years
                                                </option>
                                                <option value="3" {{ $job->experience == 3 ? 'selected' : '' }}>3 Years
                                                </option>
                                                <option value="4" {{ $job->experience == 4 ? 'selected' : '' }}>4 Years
                                                </option>
                                                <option value="5" {{ $job->experience == 5 ? 'selected' : '' }}>5 Years
                                                </option>
                                                <option value="6" {{ $job->experience == 6 ? 'selected' : '' }}>6 Years
                                                </option>
                                                <option value="7" {{ $job->experience == 7 ? 'selected' : '' }}>7 Years
                                                </option>
                                                <option value="8" {{ $job->experience == 8 ? 'selected' : '' }}>8 Years
                                                </option>
                                                <option value="9" {{ $job->experience == 9 ? 'selected' : '' }}>9 Years
                                                </option>
                                                <option value="10" {{ $job->experience == 10 ? 'selected' : '' }}>10 Years
                                                </option>
                                                <option value="10_plus" {{ $job->experience == '10_plus' ? 'selected' : '' }}>
                                                    10+ Years</option>
                                            </select>
                                            <p class="text-danger small"></p>
                                        </div>

                                        <h3 class="fs-4 mb-1 mt-5 border-top pt-5">Company Details</h3>

                                        <div class="row">
                                            <div class="mb-4 col-md-6">
                                                <label for="company_name" class="mb-2">Name<span
                                                        class="req">*</span></label>
                                                <input value="{{ $job->company_name }}" type="text"
                                                    placeholder="Company Name" id="company_name" name="company_name"
                                                    class="form-control" required>
                                                <p class="text-danger small"></p>
                                            </div>

                                            <div class="mb-4 col-md-6">
                                                <label for="company_location" class="mb-2">Location</label>
                                                <input value="{{ $job->company_location }}" type="text"
                                                    placeholder="Location" id="company_location" name="company_location"
                                                    class="form-control">
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="company_website" class="mb-2">Website</label>
                                            <input value="{{ $job->company_website }}" type="text"
                                                placeholder="Website" id="company_website" name="company_website"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="card-footer p-4">
                                        <button type="submit" class="btn btn-primary">Update Job</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('customjs')
    <script type="text/javascript">
        $('#createJobForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route('admin.job.update', $job->id) }}',
                type: 'PUT',
                dataType: 'json',
                data: $('#createJobForm').serialize(),
                success: function(response) {
                    if (response.status === true) {
                        window.location.href = '{{ route('admin.jobs') }}';
                    } else {
                        var errors = response.errors;

                        // Optimized error handling
                        $.each(errors, function(key, message) {
                            let field = $('#' + key);
                            if (message) {
                                field.addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(message);
                            } else {
                                field.removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html('');
                            }
                        });
                    }
                }
            });
        });


        function deleteUser(id) {
            if (confirm("Are you sure you want to delete?")) {
                $.ajax({
                    url: "{{ route('admin.users.destroy', '') }}/" + id, // Correct URL with user ID
                    type: 'DELETE', // Correct type
                    data: {
                        _token: "{{ csrf_token() }}", // Pass CSRF token
                    },
                    dataType: 'json',
                    success: function(response) {

                        window.location.href = "{{ route('admin.users.list') }}";
                        // if (response.status == true) {
                        //     // Redirect or refresh the page after successful deletion

                        // } else {
                        //     alert('Failed to delete user');
                        // }
                    },
                    error: function() {
                        alert('An error occurred');
                    }
                });
            }
        }
    </script>
@endsection
