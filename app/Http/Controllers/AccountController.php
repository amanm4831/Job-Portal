<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class AccountController extends Controller
{
    //
    public function Registeration(){
        return view('front.account.registeration');
    }

    public function processRegisteration(Request $request){
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
    
        
        return response()->json([
            'status' => true,
            'message' => 'Registration successful!',
        ]);
    }

    public function Login(){
        
    }
}
