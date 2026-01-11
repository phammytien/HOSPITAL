<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = [
        'warehouse_code',
        'warehouse_name',
        'location',
        'department_id',
        'is_delete'
    ];

    protected $casts = [
        'is_delete' => 'boolean',
    ];

    /**
     * Get the department that owns the warehouse
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all inventory records for this warehouse
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Get all transaction history for this warehouse
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WarehouseInventory::class);
    }
}
