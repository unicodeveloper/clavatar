<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserController extends Controller
{
    public function createUser(Request $request)
    {
        $name     = $request->get('name');
        $email    = $request->get('email');
        $password = Hash::make($request->get('password'));

        $credentials = $request->only('email');

        $user = User::where('email', $email)->exists();

        if($user) {
            return response()->json([
                'status' => false,
                'message' => 'User already exists. Please try with another email.'
            ], 401);
        }

        $user = new User;
        $user->name      = $name;
        $user->email     = $email;
        $user->password  = $password;
        $user->hash      = md5(strtolower(trim($email)));
        $user->save();

        return response()->json([
                'status' => true,
                'message' => 'User created successfully'
            ], 201);
    }

    public function loginUser(Request $request)
    {
        $email    = $request->get('email');
        $password = Hash::make($request->get('password'));

        $credentials = request(['email', 'password']);

        $user = User::where('email', $request->get('email'))->first();
        $validCredentials = Hash::check($request->get('password'), $user->password);

        if (!$validCredentials) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized. Invalid email or password'
            ], 401);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(52);
        $token->save();

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'logged_in_userId' => $user->id,
                'email' => $user->email,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]
        ]);
    }

    public function getProfile(Request $request, $hash)
    {
        $user = User::where('hash', $hash)->first();

        if(is_null($user)) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->makeHidden(['email']);

        return response()->json([
            'status' => true,
            'data'   => $user,
            'message' => 'User profile fetched successfully'
        ], 201);
    }
}
