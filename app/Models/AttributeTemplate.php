<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AttributeTemplate extends Pivot
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'attribute_template';
    
    /**
     * relasi dengan group
     *
     * @return void
     */
    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }
}
