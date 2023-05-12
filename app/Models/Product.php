<?php

namespace App\Models;

use Exception;
use Jenssegers\Mongodb\Eloquent\Model;

class Product extends Model
{
    protected $connection = 'mongodb';
}
