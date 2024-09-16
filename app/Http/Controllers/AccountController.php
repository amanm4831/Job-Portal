<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobTypes;
use App\Models\SavedJob;
use Auth;
use File;
use Hash;
use session;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AccountController extends Controller
{
    //
    public function Registeration()
    {
        return view('front.account.registeration');
    }

    // public function processRegisteration(Request $request){
    //     $user = new User();
    //     $user->name = $request->name;
    //     $user->email = $request->email;
    //     $user->password = Hash::make($request->password);

    //     $user->save();
    //     session() ->flash('succes', 'You have registered successfully');
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email', 
    //         'password' => 'required|string|min:5',
    //         'confirm_password' => 'required|same:password'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false, 
    //             'errors' => $validator->errors(),
    //         ]);
    //     }


    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Registration successful!',
    //     ]);
    // }

    public function processRegisteration(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:5',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            session()->flash('success', 'You have registered successfully');

            return response()->json([
                'status' => true,
                'message' => 'Registration successful!',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'An error occurred. Please try again.',
        ]);
    }


    public function Login()
    {
        return view('front.account.login');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return redirect()->route('account.profile');
            } else {
                return redirect()->route('account.login')->with('error', 'Either email or password is incorrect.');
            }
        } else {
            return redirect()->route('account.login')->withErrors($validator)->withInput($request->only('email'));
        }


    }


    public function profile()
    {
        $id = Auth::user()->id;
        // dd($id);
        $user = User::where('id', $id)->first();
        // dd($user);
        return view('front.account.profile', [
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $id = Auth::user()->id;

        // Correct the unique validation rule
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4|max:30',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        if ($validator->passes()) {
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->designation = $request->designation;
            $user->mobile = $request->mobile;
            $user->save();

            // Flash success message
            session()->flash('success', 'Profile updated successfully.');

            return response()->json([
                'status' => 'true',
                'errors' => [],
            ]);
        } else {
            return response()->json([
                'status' => 'false',
                'errors' => $validator->errors(),
            ]);
        }
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }

    public function updateProfilePic(Request $request)
    {
        // dd($request->all());
        $id = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'image' => 'required|image',
        ]);

        if ($validator->passes()) {
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = $id . '-' . time() . '.' . $ext;
            $image->move(public_path('profile_pic'), $imageName);
            File::delete(public_path('profile_pic/'.Auth::user()->image));
            User::where('id', $id)->update(['image' => $imageName]);

            session()->flash('success', 'image upload successful');
            return response()->json([
                'status' => true,
                'errors' => [],
            ]);

            // $sourcePath = public_path('profile_pic'.$imageName);
            // $manager = new ImageManager(Driver::class);
            // $image = $manager->read($sourcePath);

            // $image->cover(150, 150);
            // $image->toPng()->save(public_path('profile_pic/thumb'.$imageName));


            
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function createJob(){

        $categories = Category::orderBy('name', 'ASC')->where('status', 1)->get();
        $jobTypes = JobTypes::orderBy('name', 'ASC')->where('status', 1)->get();
        return view('front.account.job.create', [
            'categories' => $categories,
            'jobTypes' => $jobTypes,
        ]);
    }

    public function saveJob(Request $request) {
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
            $job = new Job();
            $job->title = $request->title;
            $job->category_id = $request->category;
            $job->job_types_id = $request->jobType;
            $job->user_id = Auth::user()->id;
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

            session()->flash('success', 'Job has been posted');
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

    public function myJobs(){
        $jobs = Job::where('user_id', Auth::user()->id)->with('JobTypes')->orderBy('created_at', 'DESC')->paginate(5);
        // dd($jobs);
        return view('front.account.job.my-jobs', [
            'jobs' => $jobs,
        ]);
    }

    public function editJob(Request $request, $id){
        // dd($id);
        $categories = Category::orderBy('name', 'ASC')->where('status', 1)->get();
        $jobTypes = JobTypes::orderBy('name', 'ASC')->where('status', 1)->get();

        $job = Job::where([
            'user_id' => Auth::user()->id,
            'id' => $id,
        ])->first();

        if($job == null){
            abort(404);
        }
        return view('front.account.job.edit', [
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            'job' => $job,
        ]);
    }

    public function updateJob(Request $request, $id) {
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
            $job->user_id = Auth::user()->id;
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

    public function deleteJob(Request $request){
        $job = Job::where([
            'user_id' => Auth::user()->id,
            'id' => $request->jobId,
        ])->first();
        if($job==null){
            session()->flash('error', 'Either job deleted or not found.');
            return response()->json([
                'status' => false,
            ]);
        }
        Job::where('id', $request->jobId)->delete();
        session()->flash('success', 'Job deleted successfully');
        return response()->json([
            'status' => true,
        ]);
    }

    public function removeJobs(Request $request){
        $JobApplication = JobApplication::where(['id'=> $request->id, 'user_id'=>Auth::user()->id])->first();

        if($JobApplication == null){
            session()->flash('error', 'Job not found');
            return response()->json([
                'status'=> false,
            ]);
        }
        JobApplication::find($request->id)->delete();
        session()->flash('success', 'Job has been removed successfully.');
        return response()->json([
            'status'=>true,
        ]);
    }

    public function savedJobs(){
        // $jobApplications = JobApplication::where('user_id', Auth::user()->id)->with(['job', 'job.JobTypes', 'job.applications'])->paginate(10);
        $savedJobs = SavedJob::where([
            'user_id' => Auth::user()->id,
        ])->with(['job', 'job.JobTypes', 'job.applications'])->orderBy('created_at', 'DESC')->paginate(10);
        return view('front.account.job.saved-jobs', [
            'savedJobs' => $savedJobs,
        ]);
    }

    public function removeSavedJob(Request $request){
        $savedJobs = SavedJob::where(['id'=> $request->id, 'user_id'=>Auth::user()->id])->first();

        if($savedJobs == null){
            session()->flash('error', 'Job not found');
            return response()->json([
                'status'=> false,
            ]);
        }
        SavedJob::find($request->id)->delete();
        session()->flash('success', 'Job has been removed successfully.');
        return response()->json([
            'status'=>true,
        ]);
    }

    public function updatePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errrors' => $validator->errors(),
            ]);
        }

        if(Hash::make($request->old_password, Auth::user()->password)==false){
            session()->flash('error', 'old password incorrect.');
            return response()->json([
                'status' => true,
            ]);
        }

        $user = Auth::user();
        $user->password = $request->new_passsword;
        $user->save();

        session()->flash('success', 'Password updated successully.');
            return response()->json([
                'status' => true,
            ]);
    }
}
