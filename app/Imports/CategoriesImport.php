<?php

namespace App\Imports;

use App\Models\ProductCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;

class CategoriesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Check if category already exists
        $existing = ProductCategory::where('category_code', $row['ma_danh_muc'])
            ->where('is_delete', false)
            ->first();
        
        if ($existing) {
            // Update existing category
            $existing->update([
                'category_name' => $row['ten_danh_muc'],
                'description' => $row['mo_ta'] ?? null,
            ]);
            return null;
        }
        
        // Create new category
        return new ProductCategory([
            'category_code' => $row['ma_danh_muc'],
            'category_name' => $row['ten_danh_muc'],
            'description' => $row['mo_ta'] ?? null,
            'is_delete' => false,
        ]);
    }
    
    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'ma_danh_muc' => 'required|string|max:50',
            'ten_danh_muc' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Check if category name already exists (excluding soft-deleted)
                    $exists = ProductCategory::where('category_name', $value)
                        ->where('is_delete', false)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Tên danh mục "' . $value . '" đã tồn tại trong hệ thống!');
                    }
                },
            ],
            'mo_ta' => 'nullable|string',
        ];
    }
    
    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'ma_danh_muc.required' => 'Mã danh mục không được để trống',
            'ten_danh_muc.required' => 'Tên danh mục không được để trống',
        ];
    }
}
