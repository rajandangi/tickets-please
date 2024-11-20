<?php

namespace App\Http\Requests\Api\V1;

use App\Models\User;
use App\Permissions\V1\Abilities;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class StoreTicketRequest extends BaseTicketRequest
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
        $authorIdAttr = 'data.relationships.author.data.id';
        $authorRule = 'required|integer|exists:users,id';

        $user = Auth::user();

        $rules = [
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.relationships' => 'required|array',
            'data.relationships.author' => 'required|array',
            'data.relationships.author.data' => 'required|array',
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
            $authorIdAttr => $authorRule . '|size:' . $user->id
        ];

        if ($user->tokenCan(Abilities::CreateTicket)) {
            $rules[$authorIdAttr] = $authorRule;
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->routeIs('authors.tickets.store')) {
            $author = $this->route('author');
            $author_id = $author instanceof User ? $author->id : $author;

            $this->merge([
                'data' => array_merge($this->data ?? [], [
                    'relationships' => [
                        'author' => [
                            'data' => ['id' => (int) $author_id]
                        ]
                    ]
                ])
            ]);
        }
    }

    /**
     * This method is used to document the request parameters for the API documentation.
     * See @https://scribe.knuckles.wtf/laravel/documenting/query-body-parameters#examples
     */
    public function bodyParameters() {
        $documentation = [
            'data.attributes.title' => [
                'description' => "The ticket's title (method)",
                'example' => 'No-example'
            ],
            'data.attributes.description' => [
                'description' => "The ticket's description",
                'example' => 'No-example',
            ],
            'data.attributes.status' => [
                'description' => "The ticket's status",
            ],
        ];

        if ($this->routeIs('tickets.store')) {
            $documentation['data.relationships.author.data.id'] = [
                'description' => 'The author assigned to the ticket.',
                'example' => 'No-example'
            ];
        } else {
            $documentation['data.relationships.author.data.id'] = [
                'description' => 'The author assigned to the ticket. Comes from the route parameter.',
                'example' => 'No-example'
            ];
        }

        return $documentation;

    }
}
