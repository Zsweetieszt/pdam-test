<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileController extends Controller
{
    /**
     * Upload file with hashing filename - REQ-B-9.1 & C-17
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'required|string|in:payment_proof,profile_photo,document',
            'compression' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $type = $request->type;
            $shouldCompress = $request->boolean('compression', false);

            // Validate file type based on upload type
            $allowedMimes = $this->getAllowedMimes($type);
            $maxSize = $this->getMaxSize($type);

            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file type for ' . $type,
                    'allowed_types' => $allowedMimes
                ], 422);
            }

            if ($file->getSize() > $maxSize) {
                return response()->json([
                    'success' => false,
                    'message' => 'File size exceeds maximum allowed size',
                    'max_size' => $maxSize . ' bytes'
                ], 422);
            }

            // Generate hashed filename - C-17
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $hashedName = hash('sha256', $originalName . time() . Str::random(16)) . '.' . $extension;

            // Define storage path based on type
            $storagePath = $this->getStoragePath($type);
            $fullPath = $storagePath . '/' . $hashedName;

            // Process file based on type
            if ($this->isImage($file) && $shouldCompress) {
                $processedFile = $this->compressImage($file, $type);
                $saved = Storage::disk('public')->put($fullPath, $processedFile);
            } else {
                $saved = $file->storeAs($storagePath, $hashedName, 'public');
            }

            if (!$saved) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save file'
                ], 500);
            }

            // Create file metadata
            $fileData = [
                'original_name' => $originalName,
                'hashed_name' => $hashedName,
                'file_path' => $fullPath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'type' => $type,
                'is_compressed' => $shouldCompress && $this->isImage($file),
                'uploaded_by' => $request->user() ? $request->user()->id : null,
                'upload_ip' => $request->ip(),
                'created_at' => now()
            ];

            // Log file upload
            if ($request->user()) {
                AuditLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'FILE_UPLOAD',
                    'table_name' => 'files',
                    'new_values' => $fileData,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => [
                    'file_path' => Storage::url($fullPath),
                    'file_name' => $hashedName,
                    'original_name' => $originalName,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'is_compressed' => $shouldCompress && $this->isImage($file)
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate file - REQ-B-9.2
     */
    public function validateFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
            'type' => 'required|string|in:payment_proof,profile_photo,document'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $type = $request->type;

            $validation = [
                'is_valid' => true,
                'file_info' => [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension()
                ],
                'validations' => []
            ];

            // Check file type
            $allowedMimes = $this->getAllowedMimes($type);
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                $validation['is_valid'] = false;
                $validation['validations'][] = [
                    'rule' => 'file_type',
                    'passed' => false,
                    'message' => 'Invalid file type',
                    'allowed' => $allowedMimes
                ];
            } else {
                $validation['validations'][] = [
                    'rule' => 'file_type',
                    'passed' => true,
                    'message' => 'File type is valid'
                ];
            }

            // Check file size
            $maxSize = $this->getMaxSize($type);
            if ($file->getSize() > $maxSize) {
                $validation['is_valid'] = false;
                $validation['validations'][] = [
                    'rule' => 'file_size',
                    'passed' => false,
                    'message' => 'File size exceeds maximum',
                    'max_size' => $maxSize
                ];
            } else {
                $validation['validations'][] = [
                    'rule' => 'file_size',
                    'passed' => true,
                    'message' => 'File size is acceptable'
                ];
            }

            // Check if image can be compressed
            if ($this->isImage($file)) {
                $validation['compression_available'] = true;
                $validation['estimated_compressed_size'] = $this->estimateCompressedSize($file);
            } else {
                $validation['compression_available'] = false;
            }

            return response()->json([
                'success' => true,
                'data' => $validation
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error validating file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download file
     */
    public function download(Request $request, $filename)
    {
        try {
            // Find file in different storage paths
            $possiblePaths = [
                'payment-proofs/' . $filename,
                'profile-photos/' . $filename,
                'documents/' . $filename
            ];

            $filePath = null;
            foreach ($possiblePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    $filePath = $path;
                    break;
                }
            }

            if (!$filePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Log file download
            if ($request->user()) {
                AuditLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'FILE_DOWNLOAD',
                    'table_name' => 'files',
                    'new_values' => ['file_path' => $filePath],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            return Storage::disk('public')->download($filePath);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error downloading file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete file
     */
    public function delete(Request $request, $filename)
    {
        try {
            // Find and delete file
            $possiblePaths = [
                'payment-proofs/' . $filename,
                'profile-photos/' . $filename,
                'documents/' . $filename
            ];

            $deleted = false;
            $deletedPath = null;
            
            foreach ($possiblePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    $deleted = true;
                    $deletedPath = $path;
                    break;
                }
            }

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Log file deletion
            if ($request->user()) {
                AuditLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'FILE_DELETE',
                    'table_name' => 'files',
                    'old_values' => ['file_path' => $deletedPath],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper methods
     */
    private function getAllowedMimes($type)
    {
        $mimeTypes = [
            'payment_proof' => [
                'image/jpeg', 'image/png', 'image/jpg', 'image/gif',
                'application/pdf'
            ],
            'profile_photo' => [
                'image/jpeg', 'image/png', 'image/jpg', 'image/gif'
            ],
            'document' => [
                'image/jpeg', 'image/png', 'image/jpg', 'image/gif',
                'application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ]
        ];

        return $mimeTypes[$type] ?? [];
    }

    private function getMaxSize($type)
    {
        $maxSizes = [
            'payment_proof' => 5 * 1024 * 1024, // 5MB
            'profile_photo' => 2 * 1024 * 1024, // 2MB
            'document' => 10 * 1024 * 1024      // 10MB
        ];

        return $maxSizes[$type] ?? 1024 * 1024; // 1MB default
    }

    private function getStoragePath($type)
    {
        $paths = [
            'payment_proof' => 'payment-proofs',
            'profile_photo' => 'profile-photos',
            'document' => 'documents'
        ];

        return $paths[$type] ?? 'uploads';
    }

    private function isImage($file)
    {
        return in_array($file->getMimeType(), [
            'image/jpeg', 'image/png', 'image/jpg', 'image/gif'
        ]);
    }

    private function compressImage($file, $type)
    {
        $quality = 80;
        $maxWidth = 1024;
        $maxHeight = 1024;

        // Adjust compression based on type
        if ($type === 'profile_photo') {
            $quality = 85;
            $maxWidth = 512;
            $maxHeight = 512;
        }

        // Create image instance
        $image = Image::make($file);

        // Resize if necessary
        if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
            $image->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Compress and encode
        return $image->encode('jpg', $quality)->getEncoded();
    }

    private function estimateCompressedSize($file)
    {
        if (!$this->isImage($file)) {
            return $file->getSize();
        }

        // Rough estimation: typically 60-80% of original size
        return round($file->getSize() * 0.7);
    }
}
