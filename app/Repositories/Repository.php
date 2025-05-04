<?php

namespace App\Repositories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * @template TModel of Model
 */
abstract class Repository
{
    /** @var string */
    public $modelClass;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return TModel|null
     */
    public function find(int $id): Model|null
    {
        return $this->modelClass::find($id);
    }

    /**
     * @return TModel|null
     */
    public function findBy(array $params): Model|null
    {
        return $this->modelClass::where($params)->first();
    }

    /**
     * @return Collection<int, TModel>
     */
    public function getBy(array $params): Collection
    {
        return $this->modelClass::where($params)->get();
    }

    /**
     * @return TModel
     */
    public function create(array $params): Model
    {
        return $this->modelClass::create($params);
    }

    /**
     * @param TModel $model
     */
    public function update(Model $model, array $params): bool
    {
        return $model->fill($params)->save();
    }

    /**
     * @param TModel $model
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
