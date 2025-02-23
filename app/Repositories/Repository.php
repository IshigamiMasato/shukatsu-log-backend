<?php

namespace App\Repositories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class Repository
{
    /** @var string */
    public $modelClass;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function find(int $id): Model|null
    {
        return $this->modelClass::find($id);
    }

    public function findBy(array $params): Model|null
    {
        return $this->modelClass::where($params)->first();
    }

    public function getBy(array $params): Collection
    {
        return $this->modelClass::where($params)->get();
    }

    public function create(array $params): Model
    {
        return $this->modelClass::create($params);
    }

    public function update(Model $model, array $params): bool
    {
        return $model->fill($params)->save();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
