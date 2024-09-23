<?php

namespace App\Http\Controllers\admin;

use App\Models\Job;
use App\Models\Category;
use App\Models\JobTypes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    //
    public function index(){
        $jobs = Job::orderBy('created_at', 'DESC')->with('user', 'applications')->paginate(10);
        return view('admin.jobs.list', [
            'jobs' => $jobs,
        ]);
    }

    public function edit($id){
        $job = Job::findOrFail($id);
        $categories = Category::orderBy('name', 'ASC')->get();
        $jobTypes = JobTypes::orderBy('name', 'ASC')->get();
        
        return view('admin.jobs.edit', [
            'job' => $job,
            'categories' => $categories,
            'jobTypes' => $jobTypes,
        ]);
    }

    public function update(Request $request, $id) {
        $rules = [
            'title' => 'required',
            'category' => 'required',
            'jobType' => 'required',
            'vacancy' => 'required|integer',
            'location' => 'required|max:20',
            'description' => 'required|max:200',
            'experience' => 'required',
            'company_name' => 'required|max:80',
        ]; 

        $validator = Validator::make($request->all(), $rules);

        if($validator->passes()){
            $job = Job::find($id);
            $job->title = $request->title;
            $job->category_id = $request->category;
            $job->job_types_id = $request->jobType;
            // $job->user_id = Auth::user()->id;
            $job->vacancy = $request->vacancy;
            $job->salary = $request->salary;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->responsibility = $request->responsibility;
            $job->qualification = $request->qualifications;
            $job->keywords = $request->keywords;
            $job->experience = $request->experience;
            $job->company_name = $request->company_name;
            $job->company_location = $request->location;
            $job->company_website = $request->website;
            $job->save();

            session()->flash('success', 'Job has been updated');
            return response()->json([
                'status' => true,
                'errors' => [],
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }
}
