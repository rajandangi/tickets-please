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
     * Get all tickets
     *
     * @group Tickets Management
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas and denote descending
     * sort with a minus sign. Example: sort=title,-updatedAt
     * @queryParam Include Author. Example: include=author
     * @queryParam filter[status] Filter by status code: A,C,H, X. No-example
     * @queryParam filter[title] Filter by title. Wildcard are supported. Example: filter[title]=*foo*
     * @queryParam filter[createdAt] Filter by created date. Date range is supported with commas.
     * Example: filter[createdAt]=2021-01-01,2021-12-31
     *
     * @responseFile responses/V1/tickets.index.json
     */
    public function index(TicketFilter $filters)
    {
        // Uses local scope to apply filters
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Create a ticket
     *
     * Create a new ticket. Users can only create tickets for themselves. Managers can create tickets for any user.
     *
     * @group Tickets Management
     *
     * @responseFile responses/V1/tickets.store.json
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
     * Show Specific Ticket
     *
     * Display an individual ticket.
     *
     * @group Tickets Management
     */
    public function show(Ticket $ticket)
    {
        if ($this->include('author')) {
            return new TicketResource($ticket->load('author'));
        }
        return new TicketResource($ticket);
    }

    /**
     * Update Ticket
     *
     * Update an existing ticket record with new data values in the storage.
     *
     * @group Tickets Management
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
     * Replace Ticket
     *
     * Replace an existing ticket record with new data values in the storage.
     *
     * @group Tickets Management
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
     * Delete Ticket
     *
     * Delete an existing ticket record from the storage.
     *
     * @group Tickets Management
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
