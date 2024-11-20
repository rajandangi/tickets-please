<?php

namespace App\Http\Requests\Api\V1;

use App\Models\User;
use App\Permissions\V1\Abilities;

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

        $user = $this->user();

        $rules = [
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
}
