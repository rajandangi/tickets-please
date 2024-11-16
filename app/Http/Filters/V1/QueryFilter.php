<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected $builder;
    protected $request;


    /**
     * QueryFilter constructor.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the query filters to the builder instance.
     *
     * The query parameters should be structured as follows:
     * [
     *     'include' => 'author',
     *     'filter' => [
     *         'status' => 'C',
     *         'title' => 'title of the ticket',
     *         'createdAt' => '2021-01-01',
     *         // other attributes can be added here
     *     ]
     * ]
     *
     * @param Builder $builder The query builder instance.
     * @return Builder The builder instance with applied filters.
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value); // also call filter method
            }
        }

        return $builder;
    }

    /**
     * Filters the query based on the provided filter attributes.
     *
     * This method iterates over the provided filter attribute array and calls the corresponding
     * method on the current instance if it exists. For example, if the key is 'status', it will
     * call the 'status' method with the value as its argument.
     *
     * @param array $filterAttributeArray An associative array where the key is the filter method name
     *                                    and the value is the filter value to be passed to the method.
     * @return void
     */
    protected function filter($filterAttributeArray)
    {
        foreach ($filterAttributeArray as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }
        $this->builder;
    }
}
