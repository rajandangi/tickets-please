<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
        if (Gate::denies('create', User::class)) {
            throw new AccessDeniedHttpException('You are not authorized to create this resource');
        }

        return new UserResource(User::create($request->mappedAttributes()));
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
    public function update(UpdateUserRequest $request, User $user)
    {
        // Policy
        if (Gate::allows('update', $user)) {
            $user->update($request->mappedAttributes());
            return new UserResource($user);
        }

        throw new AccessDeniedHttpException('You are not authorized to update this resource');
    }

    /**
     * Update the specified resource in storage.
     * Method: PUT
     * URI: /api/v1/users/{user_id}
     */
    public function replace(ReplaceUserRequest $request, User $user)
    {
        if (Gate::allows('replace', $user)) {
            $user->update($request->mappedAttributes());
            return new UserResource($user);
        }

        throw new AccessDeniedHttpException('You are not authorized to replace this resource');
    }

    /**
     * Remove the specified resource from storage.
     * Method: DELETE
     * URI: /api/v1/users/{user_id}
     */
    public function destroy(User $user)
    {
        if (Gate::denies('delete', $user)) {
            throw new AccessDeniedHttpException('You are not authorized to delete this resource');
        }

        if ($user->tickets()->exists()) {
            throw new AccessDeniedHttpException('User has tickets and cannot be deleted');
        }

        $user->delete();
        return $this->ok('User deleted successfully');
    }
}
