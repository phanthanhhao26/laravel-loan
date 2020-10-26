<?php

namespace App\Repositories;

use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Traits\ForwardsCalls;

abstract class BaseRepository
{
    use ForwardsCalls;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Application
     */
    protected $app;

    /*
     * Model class
     */
    protected $modelClass;

    /**
     * @param Application $app
     *
     * @throws \Exception
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Configure the model
     */
    public function model()
    {
        return $this->modelClass;
    }

    /**
     * Make Model instance
     *
     * @throws \Exception
     *
     * @return Model
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Build a query for retrieving all records.
     *
     * @param array $search
     * @param int|null $limit
     * @param str|null $sortColumn
     * @param str|null $sortOrder
     *
     */
    public function allQuery($search = [], $limit = null, $sortColumn = null, $sortOrder = null, $comparisonCriteria = [])
    {
        $query = $this->model->newQuery();

        $this->addSearchCriteria($search, $query, $comparisonCriteria);

        if ($limit !== null) {
            $query->limit($limit);
        }

        if ($sortColumn !== null && $sortOrder !== null) {
            $query->orderBy($sortColumn, $sortOrder);
        }

        return $query;
    }

    /**
     * We support searching via column names, defaulting to exact matches and
     * optionally supporting like, less-than or greater-than queries.
     *
     * This may be overridden by child classes when more complex criteria is
     * required.
     *
     * @param array $search
     * @param Builder $query
     * @param array $comparisonCriteria
     */
    protected function addSearchCriteria(array $search, Builder $query, array $comparisonCriteria)
    {
        foreach ($search as $key => $value) {
            if (array_key_exists($key, $comparisonCriteria)) {
                $query->where($key, $comparisonCriteria[$key], $value);
            } elseif (strpos($value, '%') !== false) {
                $query->where($key, 'like', $value);
            } else {
                $query->where($key, $value);
            }
        }
    }

    /**
     * Retrieve all records with given filter criteria
     *
     * @param array $search
     * @param int|null $limit
     * @param str|null $sortColumn
     * @param str|null $sortOrder
     * @param array $comparisonCriteria
     * @param array $columns
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all($search = [], $limit = null, $sortColumn = null, $sortOrder = null, $comparisonCriteria = [], $with = [], $columns = ['*'])
    {
        $query = $this->allQuery($search, $limit, $sortColumn, $sortOrder, $comparisonCriteria)->with($with);

        return $query->get($columns);
    }

    /**
     * Perform a search for generic index API routes with pagination support.
     *
     * @param array         $search
     * @param int|null      $limit
     * @param string|null   $sortColumn
     * @param string|null   $sortOrder
     * @param array         $columns
     */
    public function search($search = [], $limit = null, $sortColumn = null, $sortOrder = null, $comparisonCriteria = [], $with = [], $columns = ['*']): array
    {
        $metadata = [];

        if (request()->get('page') !== null) {
            $results = $this->allQuery($search, null, $sortColumn, $sortOrder, $comparisonCriteria)->with($with)->paginate($limit, $columns);
            $metadata = $results->toArray();

            unset($metadata['data']);
        } else {
            $results = $this->all($search, $limit, $sortColumn, $sortOrder, $comparisonCriteria, $with, $columns);
        }

        return ['models' => $results, 'metadata' => $metadata];
    }

    /**
     * Retrieve and paginate all records.
     *
     * @param array $search
     * @param int $limit
     * @param array $columns
     * @param str|null $sortColumn
     * @param str|null $sortOrder
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($search = [], $limit = null, $sortColumn = null, $sortOrder = null, $columns = ['*'])
    {
        $query = $this->allQuery($search, null, $sortColumn, $sortOrder, $columns);

        return $query->paginate($limit, $columns);
    }

    /**
     * Create model record
     *
     * @param array $input
     *
     * @return Model
     */
    public function create($input)
    {
        $model = $this->model->newInstance($input);

        $model->save();

        return $model;
    }

    /**
     * Find model record for given id
     *
     * @param int $id
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function find($id, $columns = ['*'])
    {
        $query = $this->model->newQuery();

        return $query->find($id, $columns);
    }

    /**
     * @param array key value of query
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function getByFields($params = [])
    {
        $query = $this->model->newQuery();

        return $query->where($params)->first();
    }

    /**
     * Update model record for given id
     *
     * @param array $input
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model
     */
    public function update($input, $id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        $model->fill($input);

        $model->save();

        return $model;
    }

    /**
     * @param int $id
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function delete($id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        return $model->delete();
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->model->newQuery(), $method, $parameters);
    }
}