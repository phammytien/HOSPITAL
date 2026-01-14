<?php

if (!function_exists('getProductImage')) {
    /**
     * Get the first product image URL
     *
     * @param int $productId
     * @param string|null $default Default image URL if no image found
     * @return string|null
     */
    function getProductImage($productId, $default = null)
    {
        $file = \App\Models\File::where('related_table', 'products')
            ->where('related_id', $productId)
            ->where('is_delete', false)
            ->oldest('id')
            ->first();

        if ($file && $file->file_path) {
            // If file_path is already a full URL, return it
            if (filter_var($file->file_path, FILTER_VALIDATE_URL)) {
                return $file->file_path;
            }

            // Check if path starts with 'products/' (New public path)
            if (str_starts_with($file->file_path, 'products/')) {
                $url = asset($file->file_path);
            }
            // Check if path starts with 'images/' (Legacy/Public path from Proposals)
            elseif (str_starts_with($file->file_path, 'images/')) {
                $url = asset($file->file_path);
            } else {
                // Default to storage path (Legacy Admin Code)
                $url = asset('storage/' . $file->file_path);
            }

            // Add timestamp to prevent browser caching when image is updated
            if ($file->uploaded_at) {
                $timestamp = strtotime($file->uploaded_at);
                $url .= '?t=' . $timestamp;
            }

            return $url;
        }

        return $default;
    }
}

if (!function_exists('getProductImages')) {
    /**
     * Get all product images
     *
     * @param int $productId
     * @return \Illuminate\Support\Collection
     */
    function getProductImages($productId)
    {
        return \App\Models\File::where('related_table', 'products')
            ->where('related_id', $productId)
            ->where('is_delete', false)
            ->orderBy('id')
            ->get()
            ->map(function ($file) {
                if (filter_var($file->file_path, FILTER_VALIDATE_URL)) {
                    $file->url = $file->file_path;
                } elseif (str_starts_with($file->file_path, 'products/')) {
                    $file->url = asset($file->file_path);
                } elseif (str_starts_with($file->file_path, 'images/')) {
                    $file->url = asset($file->file_path);
                } else {
                    $file->url = asset('storage/' . $file->file_path);
                }
                return $file;
            });
    }
}

if (!function_exists('uploadProductImage')) {
    /**
     * Upload and save product image
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $productId
     * @param int $userId
     * @return \App\Models\File|null
     */
    function uploadProductImage($file, $productId, $userId)
    {
        try {
            // IMPORTANT: Get file info BEFORE moving (temp file will be gone after move!)
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $filename = $file->hashName();

            // Save DIRECTLY to public/products (User Request)
            $destinationPath = public_path('products');

            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Move uploaded file to public directory
            $file->move($destinationPath, $filename);

            // Store relative path (just "products/filename.jpg")
            $path = 'products/' . $filename;

            \Log::info('Image uploaded successfully', [
                'filename' => $filename,
                'path' => $path,
                'original_name' => $originalName
            ]);

            // Create file record in database
            return \App\Models\File::create([
                'file_name' => $originalName,
                'file_path' => $path,
                'file_type' => $mimeType,
                'related_table' => 'products',
                'related_id' => $productId,
                'uploaded_by' => $userId,
                'uploaded_at' => now(),
                'is_delete' => false,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to upload product image: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('deleteProductImage')) {
    /**
     * Soft delete product image
     *
     * @param int $fileId
     * @return bool
     */
    function deleteProductImage($fileId)
    {
        try {
            $file = \App\Models\File::find($fileId);
            if ($file) {
                $file->is_delete = true;
                return $file->save();
            }
            return false;
        } catch (\Exception $e) {
            \Log::error('Failed to delete product image: ' . $e->getMessage());
            return false;
        }
    }
}