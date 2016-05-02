<?php
/**
 * Created by PhpStorm.
 * User: santiagogg
 * Date: 02/05/16
 * Time: 16:32
 */

namespace App\Repositories;



class ContactRepository extends BaseRepository implements ContactRepositoryInterface
{
    protected $modelName = 'App\Contact';
}