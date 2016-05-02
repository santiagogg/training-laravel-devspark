<?php
/**
 * Created by PhpStorm.
 * User: santiagogg
 * Date: 02/05/16
 * Time: 16:32
 */

namespace App\Repositories;


use App\Contact;

class ContactRepository
{
    public function all()
    {
        return Contact::all();
    }
    public function find($id)
    {
        return Contact::findOrFail($id);
    }
}