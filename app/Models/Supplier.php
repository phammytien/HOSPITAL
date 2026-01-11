<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_code',
        'supplier_name',
        'contact_person',
        'phone_number',
        'email',
        'address',
        'note',
        'is_delete',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
