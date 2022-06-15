<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Cookie extends Model
{
    protected $fillable = [
        'proxy_id', 'name', 'value',
        'domain', 'path', 'max_age',
        'expires', 'secure', 'http_only'
    ];

    public function string(): Attribute
    {
        return Attribute::make(
            get: fn($value) => sprintf(
                '%s:%s; expires:%s; path: %s; domain=%s; %s %s %s',

                $this->name,
                $this->value,
                $this->expires,
                $this->path,
                $this->domain,
                $this->max_age ? "max-age=$this->max_age;" : '',
                $this->secure ? 'Secure;' : '',
                $this->http_only ? 'HttpOnly;' : '',

            )
        );
    }
}
