<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseTicketRequest extends FormRequest
{

    /**
     * Maps request attributes to model attributes.
     *
     * This method creates an associative array where the keys are the model's attribute names
     * and the values are the corresponding values from the request. It uses a predefined
     * mapping to translate request keys to model keys.
     *
     * @return array An associative array of model attributes to update.
     */
    public function mappedAttributes(array $otherAttributes = []): array
    {
        // 'request_key' => 'model_key'
        $attributeMap = array_merge([
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.status' => 'status',
            'data.attributes.createdAt' => 'created_at',
            'data.attributes.updatedAt' => 'updated_at',
            'data.relationships.author.data.id' => 'user_id',
        ], $otherAttributes);

        $attributesToUpdate = [];
        foreach ($attributeMap as $requestKey => $ModelKey) {

            // Check if intended key exists in the request
            if ($this->has($requestKey)) {
                // Add input Value of the key to the attributesToUpdate array
                $attributesToUpdate[$ModelKey] = $this->input($requestKey);
            }
        }

        return $attributesToUpdate;
    }

    /**
     * Public method for custom validation messages
     */
    public function messages(): array
    {
        return [
            'data.attributes.status.in' => 'The status must be one of: A, C, H, X'
        ];
    }
}
