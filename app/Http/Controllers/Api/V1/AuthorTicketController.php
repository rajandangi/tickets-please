<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\V1\TicketPolicy;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AuthorTicketController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

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
    public function store(StoreTicketRequest $request, User $author)
    {
        // Policy
        if (Gate::denies('create', Ticket::class)) {
            throw new AccessDeniedHttpException('You are not authorized to create this resource');
        }

        // Create a new ticket
        return new TicketResource(Ticket::create($request->mappedAttributes()));
    }

    /**
     * Remove the specified resource from storage.
     * Method: DELETE
     * URI: /api/v1/authors/{author_id}/tickets/{ticket_id}
     */
    public function destroy(User $author, Ticket $ticket)
    {
        // Policy
        if (Gate::denies('delete', $ticket)) {
            throw new AccessDeniedHttpException('You are not authorized to delete this resource');
        }

        $ticket->delete();
        return $this->ok('Ticket deleted successfully');
    }


    /**
     * Replace the specified resource in storage.
     * Method: PUT
     * URI: /api/v1/authors/{author_id}/tickets/{ticket_id}
     */
    public function replace(ReplaceTicketRequest $request, User $author, Ticket $ticket)
    {
        if (Gate::allows('replace', $ticket)) {
            $ticket->update($request->mappedAttributes());
            return new TicketResource($ticket);
        }
        throw new AccessDeniedHttpException('You are not authorized to replace this resource');
    }

    /**
     * Update the specified resource in storage.
     * Method: PATCH
     * URI: /api/v1/authors/{author_id}/tickets/{ticket_id}
     */
    public function update(UpdateTicketRequest $request, User $author, Ticket $ticket)
    {
        if (Gate::allows('update', $ticket)) {
            $ticket->update($request->mappedAttributes());
            return new TicketResource($ticket);
        }
        throw new AccessDeniedHttpException('You are not authorized to update this resource');
    }
}
