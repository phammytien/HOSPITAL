<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_code',
        'department_id',
        'period',
        'requested_by',
        'status',
        'is_submitted', // Added
        'note',
        'is_delete',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // Alias for requester
    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(PurchaseFeedback::class);
    }

    public function workflows()
    {
        return $this->hasMany(PurchaseRequestWorkflow::class);
    }
}
