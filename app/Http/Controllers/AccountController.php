<?php

namespace App\Http\Controllers;

use Auth;
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
            User::where('id', $id)->update(['image' => $imageName]);

            $sourcePath = public_path('profile_pic'.$imageName);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($sourcePath);

            // crop the best fitting 5:3 (600x360) ratio and resize to 600x360 pixel
            $image->cover(150, 150);
            $image->toPng()->save(public_path('profile_pic/thumb'.$imageName));


            session()->flash('success', 'image upload successful');
            return response()->json([
                'status' => true,
                'errors' => [],
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }
}
