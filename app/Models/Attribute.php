<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $guarded = [];
 
    /**
     * relasi many to many dengan template
     *
     * @return void
     */
    public function templates()
    {
        return $this->belongsToMany(Template::class)->withPivot('id', 'group_name', 'status');
    }
}
