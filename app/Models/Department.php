<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_code',
        'department_name',
        'description',
        'budget_amount',
        'budget_period',
        'is_delete',
    ];

    protected $appends = ['slug'];

    public function getSlugAttribute()
    {
        $deptName = $this->department_name;
        $slug = \Illuminate\Support\Str::ascii($deptName);
        $slug = strtoupper(str_replace(' ', '_', $slug));
        $slug = preg_replace('/[^A-Z0-9_]/', '', $slug);

        if (str_starts_with($slug, 'KHOA_')) {
            $slug = substr($slug, 5);
        } elseif (str_starts_with($slug, 'PHONG_')) {
            $slug = substr($slug, 6);
        }

        return $slug;
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
