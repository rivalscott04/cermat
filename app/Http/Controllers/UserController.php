<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show($userId)
    {
        $user = User::findOrFail($userId);
        return view('user.profile', compact('user'));
    }
}
