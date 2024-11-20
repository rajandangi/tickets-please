<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Support\Facades\Auth;

class UpdateTicketRequest extends BaseTicketRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'data.attributes.title' => 'sometimes|string',
            'data.attributes.description' => 'sometimes|string',
            'data.attributes.status' => 'sometimes|string|in:A,C,H,X',
            'data.relationships.author.data.id' => 'prohibited'
        ];

        if (Auth::user()->tokenCan(Abilities::UpdateTicket)) {
            $rules['data.relationships.author.data.id'] = 'sometimes|integer|exists:users,id';
        }

        return $rules;
    }

    /**
     * Method for the documentation
     *
     * See @https://scribe.knuckles.wtf/laravel/documenting/query-body-parameters#examples
     */
    public function bodyParameters(): array
    {
        $documentation = [
            'data.attributes.title' => [
                'description' => 'The title of the ticket',
                'example' => 'This is my Title 4'
            ],
            'data.attributes.description' => [
                'description' => 'The description of the ticket',
                'example' => 'This is the Create Ticket we created'
            ],
            'data.attributes.status' => [
                'description' => 'The status of the ticket',
            ]
        ];

        if ($this->routeIs('authors.tickets.replace')) {
            $documentation['data.relationships.author.data.id'] = [
                'description' => 'The ID of the author of the ticket. This comes from the URL parameter.',
            ];
        } else {
            $documentation['data.relationships.author.data.id'] = [
                'description' => 'The ID of the author of the ticket',
            ];
        }
        return $documentation;
    }
}
