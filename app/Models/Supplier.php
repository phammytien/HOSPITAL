<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_code',
        'supplier_name',
        'contact_person',
        'phone_number',
        'email',
        'address',
        'note',
        'is_delete',
    ];

    public function categories()
    {
        return $this->belongsToMany(\App\Models\ProductCategory::class, 'category_supplier', 'supplier_id', 'product_category_id')
            ->withTimestamps();
    }

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }
}
