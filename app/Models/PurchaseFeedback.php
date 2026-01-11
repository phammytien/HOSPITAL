<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseFeedback extends Model
{
    use HasFactory;

    protected $table = 'purchase_feedbacks';
    public $timestamps = true; // Enable timestamps

    protected $fillable = [
        'purchase_order_id',
        'purchase_request_id',
        'feedback_by',
        'feedback_content',
        'feedback_date',
        'rating',
        'status',
        'admin_response',
        'response_time',
        'resolved_at',
        'is_delete',
    ];

    protected $casts = [
        'feedback_date' => 'datetime',
        'response_time' => 'datetime',
        'resolved_at' => 'datetime',
        'is_delete' => 'boolean',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function feedbackBy()
    {
        return $this->belongsTo(User::class, 'feedback_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'feedback_by');
    }
}
