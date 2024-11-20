<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ticket;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Http\Filters\V1\TicketFilter;
use App\Policies\V1\TicketPolicy;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TicketController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

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
        // Policy
        if (Gate::denies('create', Ticket::class)) {
            throw new AccessDeniedHttpException('You are not authorized to create this resource');
        }

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
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        if (Gate::denies('update', $ticket)) {
            throw new AccessDeniedHttpException('You are not authorized to update this resource');
        }

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     * Method: PUT
     * URI: /api/v1/tickets/{ticket_id}
     */
    public function replace(ReplaceTicketRequest $request, Ticket $ticket)
    {
        if (Gate::denies('replace', $ticket)) {
            throw new AccessDeniedHttpException('You are not authorized to replace this resource');
        }

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        if (Gate::denies('delete', $ticket)) {
            throw new AccessDeniedHttpException('You are not authorized to delete this resource');
        }

        $ticket->delete();
        return $this->ok('Ticket deleted successfully');
    }
}
