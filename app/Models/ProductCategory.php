<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_code',
        'category_name',
        'description',
        'is_delete',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(\App\Models\Supplier::class, 'category_supplier', 'product_category_id', 'supplier_id')
            ->withTimestamps();
    }
}
