<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ticket;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Http\Filters\V1\TicketFilter;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param TicketFilter $filters The filters to apply to the query.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(TicketFilter $filters)
    {
        // Uses local scope to apply filters
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        return new TicketResource(Ticket::create($request->mappedAttributes()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        if ($this->include('author')) {
            return new TicketResource($ticket->load('author'));
        }
        return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     * Method: PATCH
     * URI: /api/v1/tickets/{ticket_id}
     */
    public function update(UpdateTicketRequest $request, $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     * Method: PUT
     * URI: /api/v1/tickets/{ticket_id}
     */
    public function replace(ReplaceTicketRequest $request, $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->delete();
            return $this->ok('Ticket deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found', 404);
        }
    }
}
