@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Account Settings</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('front.account.sidebar')
            </div>
            <div class="col-lg-9">
                @include('front.message')
                <div class="card border-0 shadow mb-4 p-3">
                    <div class="card-body card-form">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="fs-4 mb-1">Saved Jobs</h3>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table ">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">Title</th>
                                        <th scope="col">Applicants</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border-0">
                                    @foreach ($savedJobs as $savedJob)
                                    <tr class="active">
                                        <td>
                                            <div class="job-name fw-500">{{$savedJob->job->title}}</div>
                                            <div class="info1">{{$savedJob->job->jobTypes->name}} . {{$savedJob->job->location}}</div>
                                        </td>
                                        <td>{{$savedJob->job->applications->count()}} Applications</td>
                                        <td>
                                            @if ($savedJob->job->status==1)
                                            <div class="job-status text-capitalize">Active</div>
                                            @else
                                            <div class="job-status text-capitalize">Block</div>
                                            @endif
                                            
                                        </td>
                                        <td>
                                            <div class="action-dots">
                                                <button href="#" class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="{{route("jobs.detail", $savedJob->job_id)}}"> <i class="fa fa-eye" aria-hidden="true"></i> View</a></li>
                                                    {{-- <li><a class="dropdown-item" href="{{route("jobs.detail", $jobApplication->job_id)}}"><i class="fa fa-edit" aria-hidden="true"></i> View</a></li> --}}
                                                    <li><a class="dropdown-item" href="#" onclick="removeSavedJob({{$savedJob->id}})" ><i class="fa fa-trash" aria-hidden="true"></i> Remove</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                
                            </table>
                        </div>
                        <div class="mt-4">
                            {{$savedJobs->links()}}
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
    $('#userForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: '{{ route("account.updateProfile") }}',
            type: 'PUT',
            dataType: 'json',
            data: $('#userForm').serialize(),
            success: function(response){
                if(response.status == 'true'){
                    // Clear previous error messages
                    $(".form-control").removeClass('is-invalid');
                    $("p").removeClass('invalid-feedback').html('');

                    // Redirect on success
                    window.location.href = "{{ route('account.profile') }}";
                } else {
                    // Display errors
                    var errors = response.errors;
                    if(errors.name){
                        $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.name);
                    } else {
                        $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.email){
                        $("#email").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.email);
                    } else {
                        $("#email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }
                }
            }
        });
    });

    function removeSavedJob(id){
        if(confirm("Are you sure you want to remove?")){
            $.ajax({
                url:"{{route('account.removeSavedJob')}}",
                type: 'POST',
                data: {id: id},
                dataType: 'json',
                success: function(){
                    window.location.href="{{route('account.savedJobs')}}"
                }
            })
        }
    }
</script>
@endsection
