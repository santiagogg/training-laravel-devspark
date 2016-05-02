<?php
/**
 * Created by PhpStorm.
 * User: santiagogg
 * Date: 02/05/16
 * Time: 17:21
 */

namespace App\Repositories;


class BaseRepository
{
    protected $modelName;
    public function all()
    {
        $instance = $this->getNewInstance();
        return $instance->all();
    }
    public function find($id)
    {
        $instance = $this->getNewInstance();
        return $instance->find($id);
    }
    protected function getNewInstance()
    {
        $model = $this->modelName;
        return new $model;
    }
}