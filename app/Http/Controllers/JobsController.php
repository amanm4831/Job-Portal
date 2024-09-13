<?php

namespace App\Http\Controllers;

use App\Mail\JobNotificationEmail;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobTypes;
use App\Models\SavedJob;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Mail;

class JobsController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('status', 1)->get();
        $JobTypes = JobTypes::where('status', 1)->get();
        $jobs = Job::where('status', 1);

        if (!empty($request->keyword)) {
            $jobs = $jobs->where(function ($query) use ($request) {
                $query->orWhere('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('keywords', 'like', '%' . $request->keyword . '%');
            });
        }

        if (!empty($request->location)) {
            $jobs = $jobs->where('location', $request->location);
        }

        if (!empty($request->category)) {
            $jobs = $jobs->where('category_id', $request->category);
        }

        $jobTypeArray = [];
        if (!empty($request->job_type)) { // Match the checkbox name
            $jobTypeArray = array_filter(array_unique($request->job_type));

            if (count($jobTypeArray) > 0) {
                $jobs = $jobs->whereIn('job_types_id', $jobTypeArray);
            }
        }

        if (!empty($request->experience)) {
            $jobs = $jobs->where('experience', $request->experience);
        }

        $jobs = $jobs->orderBy('created_at', 'DESC')->with('JobTypes')->paginate(9);

        // if($jobs == null){
        //     abort(404);
        // }

        return view('front.jobs', [
            'categories' => $categories,
            'JobTypes' => $JobTypes,
            'jobs' => $jobs,
            'jobTypeArray' => $jobTypeArray
        ]);
    }

    public function details($id)
    {
        $jobDetails = Job::where(['id' => $id, 'status' => 1])->with(['jobTypes', 'category'])->first();

        if ($jobDetails == null) {
            abort(404);
        }
        $count=0;
        if(Auth::user()){
            $count = SavedJob::where([
                'user_id' => Auth::user()->id,
                'job_id' => $id,
            ])->count();
        }

        $jobApplicants = JobApplication::where('job_id', $id)->with('user')->get();

        // dd($jobApplicants);
        
        return view('front.jobDetail', [
            'jobDetails' => $jobDetails,
            'count' => $count,
            'jobApplicants' => $jobApplicants,
        ]);
    }

    // public function applyJob(Request $request){
    //     $id=$request->id;
    //     $job = Job::where('id', $id)->first();

    //     if($job == null){
    //         session()->flash('error', 'job not found');
    //         return response()->json([
    //             'status' => false,
    //             'message' => "Job not found",
    //         ]);
    //     }
    //     $employer_id = $job->user_id;

    //     if($employer_id==Auth::user()->id){
    //         session()->flash('error', 'You can not apply on your own job');
    //         return response()->json([
    //             'status' => false,
    //             'message' => "You can not apply on your own job",
    //         ]);
    //     }

    //     $application = new JobApplication();
    //     $application->job_id = $id;
    //     $application->user_id = Auth::user()->id;
    //     $application->employer_id = $employer_id;
    //     $application->applied_date = now();
    //     $application->save();
    //     session()->flash('success', 'You have successfully applied');
    //         return response()->json([
    //             'status' => true,
    //             'message' => "You have successfully applied",
    //         ]);
    // }

    // public function applyJob(Request $request){
    //     $id = $request->id;
    //     $job = Job::where('id', $id)->first();

    //     if($job == null) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Job not found',
    //         ]);
    //     }

    //     $employer_id = $job->user_id;

    //     $jobApplicationCount = JobApplication::where(['job_id'=>$id, 'user_id'=>Auth::user()->id])->count();

    //     if($jobApplicationCount > 0) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'You have already applied.',
    //         ]);
    //     }

    //     if(Auth::check() && $employer_id == Auth::user()->id) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'You cannot apply for your own job',
    //         ]);
    //     }

    //     $application = new JobApplication();
    //     $application->job_id = $id;
    //     $application->user_id = Auth::user()->id;
    //     $application->employer_id = $employer_id;
    //     $application->applied_date = now();
    //     $application->save();

    //     $employer = User::where('id', $employer_id)->first();
    //     $mailData = [
    //         'employer' => $employer,
    //         'user' => Auth::user(),
    //         'job' => $job,
    //     ];

    //     Mail::to($employer->email)->send(new JobNotificationEmail($mailData));

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'You have successfully applied.',
    //     ]);
    // }

    public function applyJob(Request $request)
    {
        // Validate that the user is logged in
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'You need to be logged in to apply for a job.',
            ]);
        }

        $id = $request->id;
        $job = Job::where('id', $id)->first();

        if ($job == null) {
            return response()->json([
                'status' => false,
                'message' => 'Job not found.',
            ]);
        }

        $employer_id = $job->user_id;

        // Check if the user has already applied for this job
        $jobApplicationCount = JobApplication::where([
            'job_id' => $id,
            'user_id' => Auth::user()->id,
        ])->count();

        if ($jobApplicationCount > 0) {
            return response()->json([
                'status' => false,
                'message' => 'You have already applied for this job.',
            ]);
        }

        // Prevent user from applying for their own job
        if ($employer_id == Auth::user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot apply for your own job.',
            ]);
        }

        // Create new job application
        $application = new JobApplication();
        $application->job_id = $id;
        $application->user_id = Auth::user()->id;
        $application->employer_id = $employer_id;
        $application->applied_date = now();
        $application->save();

        // Fetch employer details
        $employer = User::where('id', $employer_id)->first();

        // Prepare mail data
        // $mailData = [
        //     'employer' => $employer,
        //     'user' => Auth::user(),
        //     'job' => $job,
        // ];

        // Mail::to($employer->email)->send(new JobNotificationEmail($mailData));

        // Success response
        return response()->json([
            'status' => true,
            'message' => 'You have successfully applied.',
        ]);
    }

    public function myJobApplications(){
        $jobApplications = JobApplication::where('user_id', Auth::user()->id)->with(['job', 'job.JobTypes', 'job.applications'])->paginate(10);
        // dd($jobs);
        return view('front.account.my-jobs-application', [
            'jobApplications' => $jobApplications,
        ]);
    }

    public function saveJobs(Request $request){
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'You need to be logged in to apply for a job.',
            ]);
        }
        $id = $request->id;
        $Job = Job::find($id);
        if($Job == null){
            return response()->json([
                'status' => false,
                'message' => 'Job not found',
            ]);
        }

        $count = SavedJob::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id,
        ])->count();

        if($count>0){
            return response()->json([
                'status'=>false,
                'message' => 'you have already save the job',
            ]);
        }

        $savedJob = new SavedJob();
        $savedJob->job_id = $id;
        $savedJob->user_id = Auth::user()->id;
        $savedJob->save();

        return response()->json([
            'status' => true,
            'message' => 'you have successfully saved the job.'
        ]);
    }
}
