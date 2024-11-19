<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;

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
    public function store(StoreTicketRequest $request, $author_id,)
    {
        // Policy
        if (Gate::denies('create', Ticket::class)) {
            return $this->error('You are not authorized to create a ticket', 403);
        }

        // Create a new ticket
        return new TicketResource(Ticket::create($request->mappedAttributes([
            'author' => 'user_id'
        ])));
    }

    /**
     * Remove the specified resource from storage.
     * Method: DELETE
     * URI: /api/v1/authors/{author_id}/tickets/{ticket_id}
     */
    public function destroy($author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            //Policy
            if(Gate::denies('delete', $ticket)) {
                return $this->error('You are not authorized to delete this ticket', 403);
            }

            $ticket->delete();

            return $this->ok('Ticket deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found', 404);
        }
    }


    /**
     * Replace the specified resource in storage.
     * Method: PUT
     * URI: /api/v1/authors/{author_id}/tickets/{ticket_id}
     */
    public function replace(ReplaceTicketRequest $request, $author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            // Policy
            if(Gate::denies('replace', $ticket)) {
                return $this->error('You are not authorized to replace this ticket', 403);
            }

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     * Method: PATCH
     * URI: /api/v1/authors/{author_id}/tickets/{ticket_id}
     */
    public function update(UpdateTicketRequest $request, $author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();


            // Policy
            if(Gate::allows('update', $ticket)) {
                $ticket->update($request->mappedAttributes());
                return new TicketResource($ticket);
            }

            return $this->error('You are not authorized to update this ticket', 403);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found', 404);
        }
    }
}
