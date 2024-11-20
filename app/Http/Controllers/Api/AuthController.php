<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Permissions\V1\Abilities;
use Illuminate\Auth\AuthenticationException;

class AuthController extends Controller
{
    use ApiResponses;

    /**
     * Login
     *
     * Authenticate user and return's user API token.
     *
     * @unauthenticated
     * @group Authentication
     *
     * @bodyParam email string required User email. Example:abcd@example.com
     * @bodyParam password string required User password.
     *
     * @responseFile responses/V1/auth.login.json
     */
    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            // 401 : HTTP Unauthorized (not logged in)
            throw new AuthenticationException('Invalid credentials');
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


    /**
     * Logout
     *
     * Logout user and delete user API token.
     *
     * @authenticated
     * @group Authentication
     *
     * @responseFile responses/V1/auth.logout.json
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->ok('Logged out');
    }
}
