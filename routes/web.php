<?php
// routes/web.php

use App\Http\Controllers\UserController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
// Temporary API routes until API routing is fixed
Route::prefix('api')->group(function () {
    // Test route
    Route::get('/test', function () {
        return response()->json([
            'message' => 'API is working via web routes!',
            'timestamp' => now(),
            'status' => 'success'
        ]);
    });
    
    // Super admins routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/users/super-admins', [UserController::class, 'getSuperAdmins']);
        Route::post('/users/super-admins', [UserController::class, 'createSuperAdmin']);
    });

    // File upload test routes
    Route::post('/test-file-upload', function (Request $request) {
        // Force PHP configuration for large file uploads
        ini_set('upload_max_filesize', '512M');
        ini_set('post_max_size', '512M');
        ini_set('max_execution_time', '600');
        ini_set('max_input_time', '600');
        ini_set('memory_limit', '1024M');

        Log::info("🧪 [TEST UPLOAD] Testing file upload via API");
        Log::info("🧪 [TEST UPLOAD] PHP limits:", [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size')
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            try {
                // Try to store the file
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('test-uploads', $fileName, 'public');
                
                return response()->json([
                    'success' => true,
                    'message' => 'File upload test successful!',
                    'file_info' => [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'size_formatted' => round($file->getSize() / 1024 / 1024, 2) . ' MB',
                        'stored_path' => $filePath
                    ],
                    'server_limits' => [
                        'upload_max_filesize' => ini_get('upload_max_filesize'),
                        'post_max_size' => ini_get('post_max_size'),
                        'max_execution_time' => ini_get('max_execution_time'),
                        'memory_limit' => ini_get('memory_limit')
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'File storage failed: ' . $e->getMessage(),
                    'file_info' => [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'size_formatted' => round($file->getSize() / 1024 / 1024, 2) . ' MB'
                    ]
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'No file uploaded or file too large',
            'received_data' => array_keys($request->all()),
            'has_file' => $request->hasFile('file'),
            'server_limits' => [
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size')
            ]
        ], 400);
    });

    // Resource upload bypass route
    Route::post('/upload-resource-bypass/{teacherId}', [TeacherController::class, 'uploadResourceBypass']);
});

// Debug and configuration routes
Route::get('/phpinfo', function () {
    ob_start();
    phpinfo();
    $phpinfo = ob_get_clean();
    
    return response($phpinfo)->header('Content-Type', 'text/html');
});

Route::get('/server-config', function () {
    return response()->json([
        'server_info' => [
            'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ],
        'php_limits' => [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
            'memory_limit' => ini_get('memory_limit'),
            'max_file_uploads' => ini_get('max_file_uploads'),
        ],
        'laravel_config' => [
            'max_upload_size' => '500MB',
            'timeout' => '600 seconds',
            'environment' => app()->environment(),
        ],
        'disk_config' => [
            'public_disk' => config('filesystems.disks.public'),
            'default_disk' => config('filesystems.default'),
        ]
    ]);
});

Route::get('/check-limits', function () {
    return response()->json([
        'server_info' => [
            'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'php_version' => PHP_VERSION,
        ],
        'php_limits' => [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
            'memory_limit' => ini_get('memory_limit'),
            'max_file_uploads' => ini_get('max_file_uploads'),
        ],
        'file_upload_test' => [
            'your_file_size' => '58.58 MB',
            'should_work' => 'Yes, if limits are properly set',
            'current_upload_limit' => ini_get('upload_max_filesize'),
            'current_post_limit' => ini_get('post_max_size')
        ]
    ]);
});

Route::get('/test-upload', function () {
    return response()->json([
        'message' => 'Upload test endpoint',
        'max_file_size' => '500MB',
        'supported_types' => ['video', 'pdf', 'document', 'link'],
        'current_limits' => [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size')
        ]
    ]);
});

// Simple HTML test form for file uploads
Route::get('/upload-test-form', function () {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Upload Test Form</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .container { max-width: 600px; margin: 0 auto; }
            .form-group { margin-bottom: 20px; }
            label { display: block; margin-bottom: 5px; font-weight: bold; }
            input[type="file"] { padding: 10px; border: 2px dashed #ccc; border-radius: 5px; width: 100%; }
            button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
            button:hover { background: #0056b3; }
            .result { margin-top: 20px; padding: 15px; border-radius: 5px; }
            .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
            .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
            .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>File Upload Test Form</h1>
            
            <div id="serverInfo" class="info result">
                Loading server information...
            </div>

            <form id="uploadForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="file">Select File to Upload:</label>
                    <input type="file" name="file" id="file" required>
                </div>
                <button type="submit">Test Upload</button>
            </form>

            <div id="result" class="result" style="display: none;"></div>

            <script>
                // Load server information
                fetch("/check-limits")
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById("serverInfo").innerHTML = `
                            <h3>Server Configuration:</h3>
                            <p><strong>Upload Max Filesize:</strong> ${data.php_limits.upload_max_filesize}</p>
                            <p><strong>Post Max Size:</strong> ${data.php_limits.post_max_size}</p>
                            <p><strong>Your Test File:</strong> 58.58 MB</p>
                            <p><strong>Status:</strong> ${parseInt(data.php_limits.upload_max_filesize) >= 500 ? "✅ Should work" : "❌ Needs configuration"}</p>
                        `;
                    });

                // Handle form submission
                document.getElementById("uploadForm").addEventListener("submit", function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData();
                    const fileInput = document.getElementById("file");
                    
                    if (fileInput.files.length === 0) {
                        alert("Please select a file");
                        return;
                    }

                    formData.append("file", fileInput.files[0]);

                    fetch("/api/test-file-upload", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        const resultDiv = document.getElementById("result");
                        resultDiv.style.display = "block";
                        
                        if (data.success) {
                            resultDiv.className = "result success";
                            resultDiv.innerHTML = `
                                <h3>✅ Upload Successful!</h3>
                                <p><strong>File:</strong> ${data.file_info.name}</p>
                                <p><strong>Size:</strong> ${data.file_info.size_formatted}</p>
                                <p><strong>Type:</strong> ${data.file_info.mime_type}</p>
                                <p><strong>Stored at:</strong> ${data.file_info.stored_path}</p>
                            `;
                        } else {
                            resultDiv.className = "result error";
                            resultDiv.innerHTML = `
                                <h3>❌ Upload Failed</h3>
                                <p><strong>Error:</strong> ${data.message}</p>
                                ${data.file_info ? `<p><strong>File:</strong> ${data.file_info.name} (${data.file_info.size_formatted})</p>` : ""}
                            `;
                        }
                    })
                    .catch(error => {
                        const resultDiv = document.getElementById("result");
                        resultDiv.style.display = "block";
                        resultDiv.className = "result error";
                        resultDiv.innerHTML = `
                            <h3>❌ Upload Error</h3>
                            <p><strong>Error:</strong> ${error.message}</p>
                        `;
                    });
                });
            </script>
        </div>
    </body>
    </html>
    ';
});

// Storage link check
Route::get('/storage-check', function () {
    $storagePath = storage_path('app/public');
    $publicPath = public_path('storage');
    
    return response()->json([
        'storage_path' => $storagePath,
        'public_storage_path' => $publicPath,
        'storage_exists' => file_exists($storagePath),
        'public_storage_exists' => file_exists($publicPath),
        'is_symlink' => is_link($publicPath),
        'storage_writable' => is_writable($storagePath),
        'public_storage_writable' => is_writable($publicPath),
    ]);
});

// Test database connection
Route::get('/db-check', function () {
    try {
        DB::connection()->getPdo();
        return response()->json([
            'success' => true,
            'message' => 'Database connection successful',
            'database' => DB::connection()->getDatabaseName()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Database connection failed: ' . $e->getMessage()
        ], 500);
    }
});

// Test file system
Route::get('/filesystem-check', function () {
    try {
        $testContent = 'Test file content ' . now();
        $testPath = 'test-file-' . time() . '.txt';
        
        // Test writing
        Storage::disk('public')->put($testPath, $testContent);
        
        // Test reading
        $readContent = Storage::disk('public')->get($testPath);
        
        // Test deletion
        $deleted = Storage::disk('public')->delete($testPath);
        
        return response()->json([
            'success' => true,
            'message' => 'Filesystem working correctly',
            'test' => [
                'written' => true,
                'read_correctly' => $readContent === $testContent,
                'deleted' => $deleted,
                'disk' => config('filesystems.default')
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Filesystem test failed: ' . $e->getMessage()
        ], 500);
    }
});

// Your existing SPA route (should be at the end)
Route::get('/{any}', function () {
    return view('welcome'); // Your Vue app entry point
})->where('any', '.*');