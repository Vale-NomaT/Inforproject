<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentProfile extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'phone',
        'relationship_to_child',
        'secondary_phone',
        'address_street',
        'address_city',
        'address_country',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Child::class, 'parent_id');
    }
}
