<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use \Spiritix\LadaCache\Database\LadaCacheTrait;
}