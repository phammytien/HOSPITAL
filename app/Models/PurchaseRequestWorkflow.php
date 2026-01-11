<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestWorkflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'action_by',
        'from_status',
        'to_status',
        'action_note',
        'action_time',
    ];

    protected $casts = [
        'action_time' => 'datetime',
    ];

    // Disable timestamps as per schema implied (only action_time?), or if not specified, maybe we don't need default timestamps?
    // Schema has 'created_at' and 'updated_at' for departments, users, purchase_requests
    // But for purchase_request_workflows it only has id, purchase_request_id, action_by, from_status, to_status, action_note, action_time.
    // It DOES NOT have created_at/updated_at columns in the definition provided.
    // So public $timestamps = false; might be needed.
    // Wait, let me double check the schema.
    // TABLE purchase_request_workflows: action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP. No updated_at.
    // So yes, I should disable Laravel standard timestamps.

    public $timestamps = false;

    // However, action_time defaults to current timestamp in DB.
    // We can also let Laravel handle it or leave it to DB. 
    // Fillable includes it so we can set it if needed.

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'action_by');
    }

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
