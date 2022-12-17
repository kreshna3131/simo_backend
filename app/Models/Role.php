<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\Permission\Models\Role as ModelsRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends ModelsRole
{
    use HasFactory;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? 'sanctum';

        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
    }
    
    /**
     * format tanggal created at
     *
     * @param  mixed $data
     * @return void
     */
    public function getCreatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y \j\a\m H:i:s');
    }
    
    /**
     * format tanggal updated at
     *
     * @param  mixed $data
     * @return void
     */
    public function getUpdatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y \j\a\m H:i:s');
    }
    
    /**
     * delegasi saat delete role
     *
     * @return void
     */
    public function delete()
    {
        $this->delegateRole();
        parent::delete();
    }

    /**
     * Delegasi ke role dengan id "1"
     */
    public function delegateRole()
    {
        $ID = 1;
        $users = $this->users()->get();

        foreach ($users as $index => $user) {
            $user->removeRole($this->name);
            $user->assignRole($ID);
        }
    }
}
