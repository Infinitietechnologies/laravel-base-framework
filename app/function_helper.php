<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function createUniqueSlug($name, $model, $excludeId = null): string
{
    $slug = Str::slug($name);
    $originalSlug = $slug;
    $count = 1;

    while (app("App\\Models\\$model")::where('slug', $slug)->when($excludeId, function ($query, $excludeId) {
        return $query->where('id', '!=', $excludeId);
    })->exists()) {
        $slug = "{$originalSlug}-{$count}";
        $count++;
    }

    return $slug;
}

function uploadFile($file, $path, $withFullPath = true, $disk = 's3'): array
{
    try {
        // Generate a unique filename to avoid overwriting existing files
        $filename = time() . '-' . $file->getClientOriginalName();
        $filename = Str::replace(' ', '-', $filename);
        // Store file in S3
        $filePath = $file->storeAs($path, $filename, $disk);
        if ($withFullPath) {
            $filePath = Storage::disk('s3')->url($filePath);
        }
        return [
            'success' => true,
            'message' => 'File uploaded successfully',
            'file_url' => $filePath
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'Upload Failed' => $e->getMessage()
        ];
    }
}

function deleteFile($file): bool
{
    $res = Storage::disk('s3')->delete($file);
    return $res;
}
