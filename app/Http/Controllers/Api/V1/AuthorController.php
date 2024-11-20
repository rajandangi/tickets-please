<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Models\User;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;

class AuthorController extends ApiController
{
    /**
     * Get authors.
     *
     * Retrieves all users that created a ticket.
     *
     * @group Showing Authors
     */
    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(User::has('tickets')->filter($filters)->paginate());
    }


    /**
     * Get an author.
     *
     * Retrieves all users that created a ticket.
     *
     * @group Showing Authors
     */
    public function show(User $author)
    {
        if ($this->include('tickets')) {
            return new UserResource($author->load('tickets'));
        }
        return new UserResource($author);
    }
}
