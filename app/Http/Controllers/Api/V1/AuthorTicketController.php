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
     * Get all tickets for a specific author.
     *
     * Retrieve a paginated list of tickets for a specific author.
     *
     * @group Managing Tickets by Author
     *
     * @responseFile responses/V1/authors.tickets.index.json
     *
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort
     * with a minus sign. Example: id,-created_at
     * @queryParam filter string Filter records using data fields. Example: filter[id]=1,2,filters[title]=*foo*
     */
    public function index($author_id, TicketFilter $filters)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)->filter($filters)->paginate()
        );
    }

    /**
     * Create a ticket
     *
     * Creates a ticket for the specific user.
     *
     * @group Managing Tickets by Author
     *
     * @urlParam author_id integer required The author's ID. No-example
     *
     * @responseFile responses/V1/authors.tickets.store.json
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
     * Delete an author's ticket
     *
     * Deletes an author's ticket.
     *
     * @group Managing Tickets by Author
     * @urlParam author_id integer required The author's ID. No-example
     * @urlParam id integer required The ticket ID. No-example
     *
     * @responseFile responses/V1/tickets.destroy.json
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
     * Replace an author's ticket
     *
     * Replaces an author's ticket.
     *
     * @group Managing Tickets by Author
     * @urlParam author_id integer required The author's ID. No-example
     * @urlParam ticket_id integer required The ticket ID. No-example
     *
     * @responseFile responses/V1/authors.tickets.replace.json
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
     * Update an author's ticket
     *
     * Updates an author's ticket.
     *
     * @group Managing Tickets by Author
     * @urlParam author_id integer required The author's ID. No-example
     * @urlParam ticket_id integer required The ticket ID. No-example
     *
     * @responseFile responses/V1/authors.tickets.replace.json
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
