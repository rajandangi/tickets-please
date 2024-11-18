<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Permissions\V1\Abilities;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            // 401 : HTTP Unauthorized (not logged in)
            return $this->error('Invalid Credentials', 401);
        }

        return $this->ok(
            'Authencated',
            [
                'token' => $user->createToken(
                    'API token for ' . $user->email,
                    Abilities::getAbilities($user),
                    now()->addMonth()
                )->plainTextToken,
            ]
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->ok('Logged out');
    }
}
