<?php
namespace App\Repositories;

abstract class BaseRepository {
    protected $model;

    public function __construct() {
        $this->makeModel();
    }

    abstract public function getFieldsSearchable(): array;

    abstract public function model(): string;

    public function makeModel() {
        $modelClass = $this->model();
        $this->model = new $modelClass;
    }

    public function allQuery(array $search = [], int $skip = null, int $limit = null) {
        $query = $this->model->newQuery();

        if (count($search)) {
            foreach ($search as $key => $value) {
                if (in_array($key, $this->getFieldsSearchable())) {
                    $query->where($key, $value); //select * from .. where color = s
                }
            }
        }

        if (!is_null($skip)) {
            $query->skip($skip);
        }

        if (!is_null($limit)) {
            $query->limit($limit);
        }

        return $query;
    }

    public function all(array $search = [], int $skip = null, int $limit = null, array $columns = ['*']) {
        $query = $this->allQuery($search, $skip, $limit);

        return $query->get($columns);
    }

    public function create(array $input) {
        $model = $this->model->newInstance($input);

        $model->save();

        return $model;
    }

    public function find(int $id, array $columns = ['*']) {
        $query = $this->model->newQuery();

        return $query->find($id, $columns);
    }

    public function findByColumns(array $conditions, array $columns = ['*']) {
        $query = $this->model->newQuery();
        
        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }
        
        return $query->first($columns);
    }

    public function update(int $id, array $input) {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        $model->fill($input);

        $model->save();

        return $model;
    }

    public function delete(int $id) {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        return $model->delete();
    }

    public function truncate() {
        return $this->model->truncate();
    }

    public function upsert(array $input, array $updateColumns = []) {
        $existingRecord = $this->findByColumns($input);

        if ($existingRecord) {
            return $this->update($existingRecord->id, $updateColumns);
        } else {
            return $this->create($updateColumns);
        }
    }
}