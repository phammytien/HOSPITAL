<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password_hash', // In Laravel default is password, but schema says password_hash. However Eloquent expects password for auth usually. 
        // Wait, the schema has password_hash. Laravel's default Auth expects 'password'. I should probably map it or just use 'password' if user allows.
        // For now I will stick to schema but Laravel might need adjustment in config/auth.php or model accessor.
        // Actually standard Laravel User model uses 'password'. The schema has 'password_hash'. 
        // I will add 'password' to fillable to be safe for standard auth, and map it.
        // Let's stick to the requested schema column names for fillable as they map to DB.
        // But for Auth to work out of the box, we might need to override getAuthPassword().
        'full_name',
        'role',
        'department_id',
        'phone_number',
        'email',
        'is_active',
        'is_delete',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Accessor for password attribute (maps to password_hash column)
     */
    public function getPasswordAttribute()
    {
        return $this->password_hash;
    }

    /**
     * Mutator for password attribute (maps to password_hash column)
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = $value;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'is_delete' => 'boolean',
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class, 'requested_by');
    }

    public function approvedOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'approved_by');
    }

    public function feedbacks()
    {
        return $this->hasMany(PurchaseFeedback::class, 'feedback_by');
    }

    public function uploadedFiles()
    {
        return $this->hasMany(File::class, 'uploaded_by');
    }

    public function workflows()
    {
        return $this->hasMany(PurchaseRequestWorkflow::class, 'action_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get the user's avatar from the files table.
     */
    public function getAvatarAttribute()
    {
        // Find the latest file associated with this user as an avatar
        // Assumes file_type 'avatar' or just matches by related_table/id
        // For simplicity and to match the controller logic we are about to write,
        // we will look for a file where related_table = 'users' and related_id = user_id
        // and sort by uploaded_at desc.

        $file = \App\Models\File::where('related_table', 'users')
            ->where('related_id', $this->id)
            ->where('is_delete', false)
            ->orderBy('uploaded_at', 'desc')
            ->first();

        return $file ? $file->file_path : null;
    }
}
