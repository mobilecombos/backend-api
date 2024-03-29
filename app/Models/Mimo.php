<?php

namespace App\Models;

/**
 * @property int  $id
 * @property int  $mimo
 * @property bool $is_ul
 */
class Mimo extends BaseModel
{
    // Disable timestamps
    public $timestamps = false;

    public $fillable = [
        'mimo',
        'is_ul',
    ];
}
