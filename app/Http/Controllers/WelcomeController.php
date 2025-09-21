<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YoutubeVideo;

class WelcomeController extends Controller
{
    public function index()
    {
        $activeVideo = YoutubeVideo::getActiveVideo();
        
        return view('welcome', compact('activeVideo'));
    }
}
