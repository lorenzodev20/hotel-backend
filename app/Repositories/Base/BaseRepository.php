<?php

declare(strict_types=1);

namespace App\Repositories\Base;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    /**
     * @var Model
     */
    protected Model $model;
    /**
     * @var array
     * Definition in child model, example: const RELATIONS = ['cars.tickets',...]
     */
    protected array $relations;


    /**
     * @param Model $model
     * @param array $relations
     */
    public function __construct(Model $model, array $relations = [])
    {
        $this->model = $model;
        $this->relations = $relations;
    }

    /**
     * @return mixed
     */
    public function all(): mixed
    {
        return $this->model->get();
    }

    /**
     * @return Builder[]|Collection
     */
    public function allWithRelations(): Collection|array
    {
        $query = $this->model;
        if (!empty($this->relations)) {
            $query = $query->with($this->relations);
        }
        return $query->get();
    }
    /**
     * @param int $id
     * @return mixed
     */
    public function get(int $id): mixed
    {
        return $this->model->find($id);
    }

    /**
     * @param Model $model
     * @return Model|bool
     */
    public function save(Model $model): Model|bool
    {
        $model->save();
        return $model;
    }

    /**
     * @param Model $model
     * @return Model|bool
     */
    public function saveQuietly(Model $model): Model|bool
    {
        $model->saveQuietly();
        return $model;
    }

    /**
     * @param Model $model
     * @return Model
     */
    public function delete(Model $model): Model
    {
        $model->delete();
        return $model;
    }

    public function samplePaginator(int $page = 1, int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->model->with($this->relations)->orderByDesc('id')->paginate(
            perPage: $perPage,
            page: $page
        );
    }
}
