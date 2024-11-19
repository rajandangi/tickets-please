<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;

class UserController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    /**
     * Display a listing of the resource.
     * Method: GET
     * URI: /api/v1/users
     */
    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(User::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     * Method: POST
     * URI: /api/v1/users
     */
    public function store(StoreUserRequest $request)
    {
        try {
            Gate::authorize('create', User::class);

            return new UserResource(User::create($request->mappedAttributes()));
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to create a user', 401);
        }
    }

    /**
     * Display the specified resource.
     * Method: GET
     * URI: /api/v1/users/{user_id}
     */
    public function show(User $user)
    {
        if ($this->include('tickets')) {
            return new UserResource($user->load('tickets'));
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     * Method: PATCH
     * URI: /api/v1/users/{user_id}
     */
    public function update(UpdateUserRequest $request, $user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            // Policy
            Gate::authorize('update', $user);

            $user->update($request->mappedAttributes());

            return new UserResource($user);
        } catch (ModelNotFoundException $ex) {
            return $this->error('User not found', 404);
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to update this user', 401);
        }
    }

    /**
     * Update the specified resource in storage.
     * Method: PUT
     * URI: /api/v1/users/{user_id}
     */
    public function replace(ReplaceUserRequest $request, string $user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            // Policy
            Gate::authorize('replace', $user);

            $user->update($request->mappedAttributes());

            return new UserResource($user);
        } catch (ModelNotFoundException $ex) {
            return $this->error('User not found', 404);
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to update this user', 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Method: DELETE
     * URI: /api/v1/users/{user_id}
     */
    public function destroy(string $user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            if ($user->tickets()->exists()) {
                return $this->error('User has associated tickets and cannot be deleted.', 400);
            }

            // Policy
            Gate::authorize('delete', $user);

            $user->delete();

            return $this->success('User deleted successfully');
        } catch (ModelNotFoundException $ex) {
            return $this->error('User not found', 404);
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to delete this user', 401);
        }
    }
}
