<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proxy extends Model
{
    public function cookies(): HasMany
    {
        return $this->hasMany(Cookie::class);
    }
}