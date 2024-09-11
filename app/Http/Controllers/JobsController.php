<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use App\Models\JobTypes;
use Illuminate\Http\Request;

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

        return view('front.jobs', [
            'categories' => $categories,
            'JobTypes' => $JobTypes,
            'jobs' => $jobs,
            'jobTypeArray' => $jobTypeArray
        ]);
    }

    public function details($id){
        $jobDetails = Job::where(['id'=> $id, 'status'=>1])->with('jobTypes')->first();
        return view('front.jobDetail',[
            'jobDetails' => $jobDetails,
        ]);
    }
}
