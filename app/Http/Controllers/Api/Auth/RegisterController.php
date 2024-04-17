<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
       $request->validate([
           'name' => 'required',
           'email' => 'required|email',
           'password' => 'required|confirmed',
           'password_confirmation' => 'required',
       ]);

       $user = User::create([
           'name' => $request->name,
           'email' => $request->email,
           'password' => bcrypt($request->password),
       ]);

       $token = $user->createToken('myapptoken');

       return (new UserResource($user))->additional([
           'token' => $token->plainTextToken,
       ]);
    }
}
