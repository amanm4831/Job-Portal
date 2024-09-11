@extends('front.layouts.app')

@section('main')
    <section class="section-3 py-5 bg-2 ">
        <div class="container">
            <div class="row">
                <div class="col-6 col-md-10 ">
                    <h2>Find Jobs</h2>
                </div>
                {{-- <div class="col-6 col-md-2">
                    <div class="align-end">
                        <select name="sort" id="sort" class="form-control">
                            <option value="1">Latest</option>
                            <option value="0">Oldest</option>
                        </select>
                    </div>
                </div> --}}
            </div>
            
            <div class="row pt-5">
                <!-- Sidebar - Search Form (3 columns) -->
                <div class="col-md-4 col-lg-3 sidebar mb-4">
                    <form action="" method="GET" name="searchForm" id="searchForm">
                        <div class="card border-0 shadow p-4">
                            <div class="mb-4">
                                <h2>Keywords</h2>
                                <input value="{{ Request::get('keyword') }}" name="keyword" id="keyword" type="text" placeholder="Keywords" class="form-control">
                            </div>

                            <div class="mb-4">
                                <h2>Location</h2>
                                <input value="{{ Request::get('location') }}" name="location" id="location" type="text" placeholder="Location" class="form-control">
                            </div>

                            <div class="mb-4">
                                <h2>Category</h2>
                                <select name="category" id="category" class="form-control">
                                    <option value="">Select a Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ (Request::get('category') == $category->id) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <h2>Job Type</h2>
                                @foreach ($JobTypes as $JobType)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="job_type[]" value="{{ $JobType->id }}" id="job-type-{{ $JobType->id }}"
                                            {{ in_array($JobType->id, $jobTypeArray) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="job-type-{{ $JobType->id }}">{{ $JobType->name }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-4">
                                <h2>Experience</h2>
                                <select name="experience" id="experience" class="form-control">
                                    <option value="">Select Experience</option>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ (Request::get('experience') == $i) ? 'selected' : '' }}>
                                            {{ $i }} Year{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                    <option value="10_plus" {{ (Request::get('experience') == '10_plus') ? 'selected' : '' }}>10+ Years</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="{{route('jobs')}}" class="btn btn-secondary mt-2">Reset</a>
                        </div>
                    </form>
                </div>

                <!-- Job Listings (9 columns) -->
                <div class="col-md-8 col-lg-9">
                    <div class="job_listing_area">
                        <div class="job_lists">
                            <div class="row">
                                @foreach ($jobs as $job)
                                    <div class="col-md-4">
                                        <div class="card border-0 p-3 shadow mb-4">
                                            <div class="card-body">
                                                <h3 class="border-0 fs-5 pb-2 mb-0">{{ $job->title }}</h3>
                                                <p>{{ Str::words($job->description, 5) }}</p>
                                                <div class="bg-light p-3 border">
                                                    <p class="mb-0">
                                                        <span class="fw-bolder"><i class="fa fa-map-marker"></i></span>
                                                        <span class="ps-1">{{ $job->location }}</span>
                                                    </p>
                                                    <p class="mb-0">
                                                        <span class="fw-bolder"><i class="fa fa-clock-o"></i></span>
                                                        <span class="ps-1">{{ $job->jobTypes->name }}</span>
                                                    </p>
                                                    @if (!is_null($job->salary))
                                                        <p class="mb-0">
                                                            <span class="fw-bolder"><i class="fa fa-usd"></i></span>
                                                            <span class="ps-1">{{ $job->salary }} PA</span>
                                                        </p>
                                                    @endif
                                                </div>

                                                <div class="d-grid mt-3">
                                                    <a href="{{route('jobs.detail', $job->id)}}" class="btn btn-primary btn-lg">Details</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                {{ $jobs->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
<script>
    $("#searchForm").submit(function(e){
        e.preventDefault(); // Prevent default form submission

        var url = "{{ route('jobs') }}";
        
        var keyword = $("#keyword").val().trim();
        var location = $("#location").val().trim();
        var category = $("#category").val().trim();
        var experience = $("#experience").val().trim();

        var jobTypes = [];
        $("input[name='job_type[]']:checked").each(function(){
            jobTypes.push($(this).val());
        });

        var queryParams = {};

        if (keyword !== '') {
            queryParams['keyword'] = keyword;
        }

        if (location !== '') {
            queryParams['location'] = location;
        }

        if (category !== '') {
            queryParams['category'] = category;
        }

        if (experience !== '') {
            queryParams['experience'] = experience;
        }

        if (jobTypes.length > 0) {
            queryParams['job_type'] = jobTypes.join(',');
        }

        var queryString = $.param(queryParams);

        $.ajax({
            url: url,
            type: 'GET',
            data: queryParams,
            success: function(response) {
                $(".job_listing_area").html(response);
            },
            error: function(xhr) {
                console.log('An error occurred: ' + xhr.statusText);
            }
        });
    });
</script>
@endsection
