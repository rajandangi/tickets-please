<?php

namespace App\Http\Filters\V1;

class TicketFilter extends QueryFilter
{
    protected $sortable = [
        'id',
        'title',
        'status',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];
    /**
     * Filter the query by the created_at date.
     *
     * This method filters the query based on the created_at date. If the provided value
     * contains multiple dates separated by a comma, it will filter the query to include
     * records where the created_at date is between the specified dates. If a single date
     * is provided, it will filter the query to include records where the created_at date
     * matches the specified date.
     *
     * @param string $value The date or date range to filter by. If a date range is provided,
     *                      it should be in the format 'YYYY-MM-DD,YYYY-MM-DD'.
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance with the applied filter.
     */
    public function createdAt($value)
    {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('created_at', $dates);
        }
        return $this->builder->whereDate('created_at', $dates[0]);
    }

    /**
     * Include related models in the query.
     *
     * @param string|array $value The related models to include.
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance with the included relationships.
     */
    public function include($value)
    {
        $includedRelations = 'author';

        if (strtolower($value) === $includedRelations) {
            return $this->builder->with($includedRelations);
        }

        return $this->builder;
    }

    /**
     * Filter the query by status.
     *
     * This method filters the query results based on the provided status values.
     * It expects a comma-separated string of status values, which will be split
     * into an array and used to filter the query using the `whereIn` clause.
     *
     * @param string $value A comma-separated string of status values.
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance with the applied status filter.
     */
    public function status($value)
    {
        return $this->builder->whereIn('status', explode(',', $value));
    }


    /**
     * Filter the query by the title.
     *
     * This method replaces any asterisks (*) in the provided value with
     * percentage signs (%) to create a SQL LIKE pattern and applies it
     * to the query builder.
     *
     * @param string $value The value to filter the title by, with asterisks
     *                      representing wildcard characters.
     * @return \Illuminate\Database\Eloquent\Builder The query builder with the
     *                                               title filter applied.
     */
    public function title($value)
    {
        $likeStr = str_replace('*', '%', $value);
        return $this->builder->where('title', 'like', $likeStr);
    }

    /**
     * Filter the query by updated_at date.
     *
     * This method filters the query based on the updated_at date. If the provided value
     * contains multiple dates separated by a comma, it will filter the query to include
     * records where the updated_at date is between the specified dates. If a single date
     * is provided, it will filter the query to include records where the updated_at date
     * matches the specified date.
     *
     * @param string $value The date or date range to filter by. If a date range is provided,
     *                     it should be in the format 'YYYY-MM-DD,YYYY-MM-DD'.
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance with the applied filter.
     */
    public function updatedAt($value)
    {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('updated_at', $dates);
        }
        return $this->builder->whereDate('updated_at', $dates[0]);
    }
}
