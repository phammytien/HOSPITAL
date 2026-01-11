<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'order_code',
        'purchase_request_id',
        'department_id',
        'approved_by',
        'order_date',
        'expected_delivery_date',
        'total_amount',
        'status',
        'is_delete',
        'ordered_at',
        'shipping_at',
        'delivered_at',
        'completed_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'order_date' => 'datetime',
        'expected_delivery_date' => 'datetime',
        'ordered_at' => 'datetime',
        'shipping_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(PurchaseFeedback::class);
    }
}
