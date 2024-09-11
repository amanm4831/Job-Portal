<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use App\Models\JobTypes;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function index(){

        $categories = Category::where('status', 1)->orderBy('name', 'ASC')->take(8)->get();
        $FeaturedJobs = Job::where('status',1)->where('isFeatured', 1)->orderBy('created_at', 'DESc')->with('JobTypes')->take(6)->get();
        $LatestJobs = Job::where('status',1)->orderBy('created_at', 'DESc')->with('JobTypes')->take(6)->get();
        return view('front.home',[
            'categories' => $categories,
            'FeaturedJobs' => $FeaturedJobs,
            'LatestJobs' => $LatestJobs,
        ]);
    }
}
