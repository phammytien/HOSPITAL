<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    public $timestamps = false; // Table doesn't have created_at/updated_at

    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'related_table',
        'related_id',
        'uploaded_by',
        'uploaded_at',
        'is_delete',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
