<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthorTicketController extends ApiController
{
    /**
     * Display a paginated listing of the tickets for a specific author.
     * Method: GET
     * URI: /api/v1/authors/{author_id}/tickets
     *
     * @param int $author_id The ID of the author whose tickets are to be retrieved.
     * @param \App\Filters\TicketFilter $filters The filters to apply to the ticket query.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection A collection of ticket resources.
     */
    public function index($author_id, TicketFilter $filters)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)->filter($filters)->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     * Method: POST
     * URI: /api/v1/authors/{author_id}/tickets
     */
    public function store($author_id, StoreTicketRequest $request)
    {
        try {
            User::findOrFail($author_id);
        } catch (ModelNotFoundException $exception) {
            return $this->error('User not found', 422);
        }

        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $author_id,
        ];

        return new TicketResource(Ticket::create($model));
    }
}
