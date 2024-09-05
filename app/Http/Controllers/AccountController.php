<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use App\Models\JobTypes;
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
        // Validate the request data first
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
            'title' => 'required|max:200',
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
        return view('front.account.job.my-jobs');
    }
}
