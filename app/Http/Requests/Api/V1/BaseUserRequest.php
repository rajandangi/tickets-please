<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseUserRequest extends FormRequest
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
            'data.attributes.name' => 'name',
            'data.attributes.email' => 'email',
            'data.attributes.password' => 'password',
            'data.attributes.isManager' => 'is_manager',
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
}
