<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    /**
     * @var string
     */
    protected $table = 'equipment_categories';

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'prefix', 'description'
    ];

    /**
     * Relation with models.
     */
    public function models()
    {
        return $this->hasMany(EquipmentModel::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function equipments()
    {
        return $this->hasManyThrough(Equipment::class, EquipmentModel::class);
    }
}
