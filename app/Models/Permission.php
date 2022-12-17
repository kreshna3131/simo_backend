<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as ModelsPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Permission extends ModelsPermission
{
    use HasFactory, HasRoles;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? 'sanctum';

        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
    }
}
