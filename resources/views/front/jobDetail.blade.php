@extends('front.layouts.app')
@section('main')
    <section class="section-4 bg-2">
        <div class="container pt-5">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb" class=" rounded-3 p-3">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('jobs') }}"><i class="fa fa-arrow-left"
                                        aria-hidden="true"></i> &nbsp;Back to Jobs</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="container job_details_area">
            <div class="row pb-5">
                <div class="col-md-8">
                    @include('front.message')
                    <div class="card shadow border-0">
                        <div class="job_details_header">
                            <div class="single_jobs white-bg d-flex justify-content-between">
                                <div class="jobs_left d-flex align-items-center">

                                    <div class="jobs_conetent">
                                        <a href="#">
                                            <h4>{{ $jobDetails->title }}</h4>
                                        </a>
                                        <div class="links_locat d-flex align-items-center">
                                            <div class="location">
                                                <p> <i class="fa fa-map-marker"></i> {{ $jobDetails->location }}</p>
                                            </div>
                                            <div class="location">
                                                <p> <i class="fa fa-clock-o"></i> Part-time</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="jobs_right">
                                    <div class="apply_now">
                                        <a class="heart_mark" href="#"> <i class="fa fa-heart-o"
                                                aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="descript_wrap white-bg">
                            <div class="single_wrap">
                                <h4>Job description</h4>
                                <p>{{ $jobDetails->description }}</p>
                                {{-- <p>Variations of passages of lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing.</p> --}}
                            </div>
                            <div class="single_wrap">
                                
                                <ul>
                                    @if (!empty($jobDetails->responsibility))
                                    <h4>Responsibility</h4>
                                        <p>{{ $jobDetails->responsibility }}</p>
                                    @endif

                                    {{-- <li>Have sound knowledge of commercial activities.</li>
                                <li>Leadership, analytical, and problem-solving abilities.</li>
                                <li>Should have vast knowledge in IAS/ IFRS, Company Act, Income Tax, VAT.</li> --}}
                                </ul>
                            </div>
                            <div class="single_wrap">
                                
                                <ul>
                                    @if (!empty($jobDetails->qualification))
                                    <h4>Qualifications</h4>
                                        <p>{{ $jobDetails->qualification }}</p>
                                    @endif
                                    {{-- <li>Have sound knowledge of commercial activities.</li>
                                <li>Leadership, analytical, and problem-solving abilities.</li>
                                <li>Should have vast knowledge in IAS/ IFRS, Company Act, Income Tax, VAT.</li> --}}
                                </ul>
                            </div>
                            <div class="single_wrap">
                                
                                @if (!empty($jobDetails->benefits))
                                <h4>Benefits</h4>
                                    <p>{{ $jobDetails->benefits }}</p>
                                @endif
                            </div>
                            <div class="border-bottom"></div>
                            <div class="pt-3 text-end">
                                @if (Auth::check())
                                <a href="#" onclick="applyJob({{$jobDetails->id}})" class="btn btn-primary">Save</a>
                                @else
                                <a href="javascript:void(0)" class="btn btn-primary disabled">Login To Save</a>
                                @endif
                                {{-- <a href="#" class="btn btn-secondary">Save</a> --}}
                                @if (Auth::check())
                                <a href="#" onclick="applyJob({{$jobDetails->id}})" class="btn btn-primary">Apply</a>
                                @else
                                <a href="javascript:void(0)" class="btn btn-primary disabled">Login To Apply</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow border-0">
                        <div class="job_sumary">
                            <div class="summery_header pb-1 pt-4">
                                <h3>Job Summary</h3>
                            </div>
                            <div class="job_content pt-3">
                                <ul>
                                    <li>Published on:
                                        <span>{{ \carbon\carbon::parse($jobDetails->created_at)->format('d, M Y') }}</span>
                                    </li>
                                    <li>Vacancy: <span>{{ $jobDetails->vacancy }} Position</span></li>
                                    @if (!empty($jobDetails->salary))
                                    <li>Salary:<span>{{ $jobDetails->salary }} PA</span></li>
                                        @endif
                                    
                                    <li>Location: <span>{{ $jobDetails->location }}</span></li>
                                    <li>Job Nature: <span> {{ $jobDetails->jobtypes->name }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow border-0 my-4">
                        <div class="job_sumary">
                            <div class="summery_header pb-1 pt-4">
                                <h3>Company Details</h3>
                            </div>
                            <div class="job_content pt-3">
                                <ul>
                                    <li>Name: <span>{{ $jobDetails->company_name }}</span></li>
                                    <li>Locaion: <span>{{ $jobDetails->company_location }}</span></li>
                                    @if (!empty($jobDetails->company_website))
                                    <li>Webite: <span>{{ $jobDetails->company_website }}</span></li>
                                        @endif
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('customjs')
<script type="text/javascript">
   function applyJob(id) {
    if (confirm("Are you sure you want to apply for this job?")) {
        $.ajax({
            url: "{{ route('jobs.apply') }}",
            type: 'POST',
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                // Check if the response has a success or error message
                if (response.status) {
                    // Display success message
                    displayMessage('success', response.message);
                } else {
                    // Display error message
                    displayMessage('danger', response.message);
                }
            },
            error: function(xhr) {
                displayMessage('danger', 'An error occurred. Please try again later.');
            }
        });
    }
}

// Function to dynamically display messages
function displayMessage(type, message) {
    let messageContainer = document.getElementById('flash-message');

    // If there's already a flash message container, clear it and reuse it
    if (!messageContainer) {
        messageContainer = document.createElement('div');
        messageContainer.id = 'flash-message';
        document.body.prepend(messageContainer); // You can prepend this to a specific container too
    }

    messageContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Optionally reload the page after showing the message (if needed)
    setTimeout(function() {
        window.location.reload();
    }, 3000); // Reload the page after 3 seconds
}

</script>
@endsection

