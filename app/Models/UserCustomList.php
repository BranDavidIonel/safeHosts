<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCustomList extends Model
{
    protected $guarded = [];
    public  function host()
    {
        return $this->belongsTo(Host::class);

    }
    public  function user()
    {
        return $this->belongsTo(User::class);
    }
}
