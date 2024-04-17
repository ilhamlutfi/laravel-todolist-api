<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->attempt($credentials)) {
            return response([
                'message' => 'Your credentials are invalid.',
            ], 401);
        }

        $user = auth()->user();

        return (new UserResource($user))->additional([
            'token' => $user->createToken('myapptoken')->plainTextToken
        ]);
    }
}
