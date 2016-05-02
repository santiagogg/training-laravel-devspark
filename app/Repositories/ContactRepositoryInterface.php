<?php
/**
 * Created by PhpStorm.
 * User: santiagogg
 * Date: 02/05/16
 * Time: 16:57
 */

namespace App\Repositories;


interface ContactRepositoryInterface
{
    public function all();
    public function find($id);

}