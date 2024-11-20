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
     * Get all users
     *
     * @group Users Management
     *
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas and denote descending
     * sort with a minus sign. Example: sort=name,-createdAt
     * @queryParam include Include related data. Example: include=tickets
     * @queryParam filter[name] Filter by name. Wildcard are supported. Example: filter[name]=*foo*
     * @queryParam filter[email] Filter by email. Wildcard are supported. Example: filter[email]=*foo*
     *
     * @responseFile responses/V1/users.index.json
     */
    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(User::filter($filters)->paginate());
    }

    /**
     * Create a user
     *
     * @group Users Management
     * @responseFile responses/V1/users.store.json
     */
    public function store(StoreUserRequest $request)
    {
        if (Gate::denies('create', User::class)) {
            throw new AccessDeniedHttpException('You are not authorized to create this resource');
        }

        return new UserResource(User::create($request->mappedAttributes()));
    }

    /**
     * Display a specific user
     *
     * @group Users Management
     *
     * @responseFile responses/V1/users.show.json
     */
    public function show(User $user)
    {
        if ($this->include('tickets')) {
            return new UserResource($user->load('tickets'));
        }

        return new UserResource($user);
    }

    /**
     * Update a user
     *
     * @group Users Management
     *
     * @responseFile responses/V1/users.update.json
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
     * Replace a user
     *
     * @group Users Management
     *
     * @responseFile responses/V1/users.replace.json
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
     * Delete a user
     *
     * @group Users Management
     *
     * @responseFile responses/V1/users.destroy.json
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
