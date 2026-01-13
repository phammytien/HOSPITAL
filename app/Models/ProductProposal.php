<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'description',
        'department_id',
        'created_by',
        'status',
        'rejection_reason',
        'buyer_id',
        'approver_id',
        'product_code',
        'category_id',
        'unit',
        'unit_price',
        'supplier_id',
        'product_id',
        'is_delete',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_delete' => 'boolean',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopeNotDeleted($query)
    {
        return $query->where('is_delete', false);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeCreated($query)
    {
        return $query->where('status', 'CREATED');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    // Image relationship (using files table like products)
    public function images()
    {
        return $this->hasMany(File::class, 'related_id')
            ->where('related_table', 'product_proposals')
            ->where('is_delete', false)
            ->orderBy('id');
    }

    public function primaryImage()
    {
        return $this->hasOne(File::class, 'related_id')
            ->where('related_table', 'product_proposals')
            ->where('is_delete', false)
            ->oldest('id');
    }
}
