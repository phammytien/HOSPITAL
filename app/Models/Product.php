<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    public $timestamps = false; // DB table doesn't have updated_at

    protected $fillable = [
        'product_code',
        'product_name',
        'category_id',
        'unit',
        'unit_price',
        'stock_quantity',
        'description',
        'supplier_id',
        'is_delete',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function purchaseRequestItems()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Get all images for this product
     */
    public function images()
    {
        return $this->hasMany(File::class, 'related_id')
            ->where('related_table', 'products')
            ->where('is_delete', false)
            ->orderBy('id');
    }

    /**
     * Get the primary (first) image for this product
     */
    public function primaryImage()
    {
        return $this->hasOne(File::class, 'related_id')
            ->where('related_table', 'products')
            ->where('is_delete', false)
            ->oldest('id');
    }
}
