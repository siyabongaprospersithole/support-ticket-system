<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Config;
use Exception;

class SetupController extends Controller
{
    /**
     * Display the setup view
     */
    public function index()
    {
        try {
            // Check if setup has already been completed
            if (file_exists(storage_path('app/setup_completed'))) {
                return redirect('/');
            }

            // Disable Vite HMR temporarily during setup
            $this->updateEnv(['VITE_DISABLE_HMR' => 'true']);

            // Get environment information
            $envInfo = [
                'phpVersion' => PHP_VERSION,
                'requirements' => [
                    'php' => version_compare(PHP_VERSION, '8.1.0', '>='),
                    'pdo' => extension_loaded('pdo'),
                    'mbstring' => extension_loaded('mbstring'),
                    'tokenizer' => extension_loaded('tokenizer'),
                    'xml' => extension_loaded('xml'),
                    'ctype' => extension_loaded('ctype'),
                    'json' => extension_loaded('json'),
                    'bcmath' => extension_loaded('bcmath'),
                    'fileinfo' => extension_loaded('fileinfo')
                ],
                'writablePaths' => [
                    'storage' => is_writable(storage_path()),
                    'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
                    '.env' => is_writable(base_path('.env')) || is_writable(base_path())
                ]
            ];

            return Inertia::render('Setup/Index', [
                'envInfo' => $envInfo,
                'dbConfig' => [
                    'connection' => env('DB_CONNECTION', 'mysql'),
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'port' => env('DB_PORT', '3306'),
                    'database' => env('DB_DATABASE', 'laravel'),
                    'username' => env('DB_USERNAME', 'root'),
                    'password' => env('DB_PASSWORD', '')
                ],
                'mailConfig' => [
                    'host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
                    'port' => env('MAIL_PORT', '2525'),
                    'username' => env('MAIL_USERNAME', ''),
                    'password' => env('MAIL_PASSWORD', ''),
                    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                    'from_address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                    'from_name' => env('MAIL_FROM_NAME', 'Support Ticket System')
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Setup index error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Inertia::render('Error', [
                'message' => 'Failed to load setup page. Please check server logs for more information.'
            ]);
        }
    }

    /**
     * Test database connection
     */
    public function testDatabase(Request $request)
    {
        try {
            $validated = $request->validate([
                'connection' => 'required|in:mysql,pgsql,sqlite,sqlsrv',
                'host' => 'required_unless:connection,sqlite',
                'port' => 'required_unless:connection,sqlite',
                'database' => 'required',
                'username' => 'required_unless:connection,sqlite',
                'password' => 'nullable',
            ]);

            // Test connection based on provided credentials
            $connection = $validated['connection'];
            
            $config = [
                'driver' => $connection,
                'database' => $validated['database'],
            ];
            
            if ($connection !== 'sqlite') {
                $config['host'] = $validated['host'];
                $config['port'] = $validated['port'];
                $config['username'] = $validated['username'];
                $config['password'] = $validated['password'];
            }
            
            // Create a temporary connection with the provided config
            $tempConnection = DB::connection()->getDriverName();
            Config::set("database.connections.setup_test", $config);
            
            // Test the connection
            DB::connection('setup_test')->getPdo();
            
            return response()->json(['success' => true, 'message' => 'Successfully connected to the database!']);
        } catch (Exception $e) {
            Log::error('Database connection test failed', [
                'error' => $e->getMessage(),
                'connection' => $request->connection ?? 'unknown',
                'database' => $request->database ?? 'unknown'
            ]);
            
            return response()->json(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()], 422);
        } finally {
            // Remove the temporary connection
            if (isset($tempConnection)) {
                DB::disconnect('setup_test');
            }
        }
    }

    /**
     * Save database configuration
     */
    public function saveDatabase(Request $request)
    {
        try {
            $validated = $request->validate([
                'connection' => 'required|in:mysql,pgsql,sqlite,sqlsrv',
                'host' => 'required_unless:connection,sqlite',
                'port' => 'required_unless:connection,sqlite',
                'database' => 'required',
                'username' => 'required_unless:connection,sqlite',
                'password' => 'nullable',
            ]);

            // Update .env file with database configuration
            $this->updateEnv([
                'DB_CONNECTION' => $validated['connection'],
                'DB_HOST' => $validated['host'],
                'DB_PORT' => $validated['port'],
                'DB_DATABASE' => $validated['database'],
                'DB_USERNAME' => $validated['username'],
                'DB_PASSWORD' => $validated['password']
            ]);

            return response()->json(['success' => true, 'message' => 'Database configuration saved successfully!']);
        } catch (Exception $e) {
            Log::error('Failed to save database configuration', [
                'error' => $e->getMessage(),
                'connection' => $request->connection ?? 'unknown',
                'database' => $request->database ?? 'unknown'
            ]);
            
            return response()->json(['success' => false, 'message' => 'Failed to save database configuration: ' . $e->getMessage()], 422);
        }
    }

    /**
     * Run database migrations
     */
    public function runMigrations()
    {
        try {
            DB::beginTransaction();
            
            // Run migrations
            Artisan::call('migrate', ['--force' => true]);
            
            // Seed the database with initial data
            Artisan::call('db:seed', ['--force' => true]);
            
            DB::commit();
            
            return response()->json([
                'success' => true, 
                'message' => 'Migrations completed successfully!',
                'output' => Artisan::output()
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['success' => false, 'message' => 'Migration failed: ' . $e->getMessage()], 422);
        }
    }

    /**
     * Save mail configuration
     */
    public function saveMail(Request $request)
    {
        try {
            $validated = $request->validate([
                'host' => 'required',
                'port' => 'required',
                'username' => 'required',
                'password' => 'required',
                'encryption' => 'required|in:tls,ssl,null',
                'from_address' => 'required|email',
                'from_name' => 'required',
            ]);

            // Update .env file with mail configuration
            $this->updateEnv([
                'MAIL_MAILER' => 'smtp',
                'MAIL_HOST' => $validated['host'],
                'MAIL_PORT' => $validated['port'],
                'MAIL_USERNAME' => $validated['username'],
                'MAIL_PASSWORD' => $validated['password'],
                'MAIL_ENCRYPTION' => $validated['encryption'] === 'null' ? null : $validated['encryption'],
                'MAIL_FROM_ADDRESS' => $validated['from_address'],
                'MAIL_FROM_NAME' => $validated['from_name']
            ]);

            return response()->json(['success' => true, 'message' => 'Mail configuration saved successfully!']);
        } catch (Exception $e) {
            Log::error('Failed to save mail configuration', [
                'error' => $e->getMessage(),
                'host' => $request->host ?? 'unknown'
            ]);
            
            return response()->json(['success' => false, 'message' => 'Failed to save mail configuration: ' . $e->getMessage()], 422);
        }
    }

    /**
     * Finalize setup
     */
    public function finalize()
    {
        try {
            // Clear application cache
            Artisan::call('optimize:clear');
            
            // Mark setup as completed
            File::put(storage_path('app/setup_completed'), date('Y-m-d H:i:s'));
            
            // Re-enable Vite HMR once setup is complete
            $this->updateEnv(['VITE_DISABLE_HMR' => 'false']);
            
            return response()->json(['success' => true, 'message' => 'Setup completed successfully!']);
        } catch (Exception $e) {
            Log::error('Setup finalization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['success' => false, 'message' => 'Setup finalization failed: ' . $e->getMessage()], 422);
        }
    }

    /**
     * Helper method to update .env file
     */
    private function updateEnv(array $values)
    {
        // If we're handling multiple env updates in one request, use a lock file
        // to prevent multiple server restarts
        $lockFile = storage_path('app/env_update_lock');
        
        try {
            // Early return if we already updated env in this request
            if (file_exists($lockFile) && (time() - filemtime($lockFile)) < 5) {
                return;
            }
            
            // Create a lock file indicating we're updating the env
            touch($lockFile);
            
            $envFilePath = base_path('.env');
            
            if (!file_exists($envFilePath)) {
                // Create .env file from .env.example if it doesn't exist
                copy(base_path('.env.example'), $envFilePath);
            }
            
            // Read the original content of the .env file
            $envContent = file_get_contents($envFilePath);
            $newEnvContent = $envContent;
            
            foreach ($values as $key => $value) {
                // Check if the value is actually changing
                $currentValue = env($key);
                if ($currentValue === $value) {
                    // Skip unchanged values to reduce file writes
                    continue;
                }
                
                // If the value contains spaces, wrap it in quotes
                if (strpos($value, ' ') !== false) {
                    $value = '"' . $value . '"';
                }
                
                // Replace existing values or add new ones
                if (preg_match("/^{$key}=/m", $newEnvContent)) {
                    $newEnvContent = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $newEnvContent);
                } else {
                    $newEnvContent .= PHP_EOL . "{$key}={$value}";
                }
            }
            
            // Only write if content actually changed
            if ($newEnvContent !== $envContent) {
                // Create a temporary file with the new content
                $tempFile = storage_path('app/temp_env_' . time());
                file_put_contents($tempFile, $newEnvContent);
                
                // Rename the temporary file to .env, which is an atomic operation
                rename($tempFile, $envFilePath);
                
                // Update the environment variables in the current process
                foreach ($values as $key => $value) {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        } catch (Exception $e) {
            Log::error('Failed to update .env file', [
                'error' => $e->getMessage(),
                'values' => array_keys($values)
            ]);
            
            throw $e; // Re-throw the exception to be handled by the calling method
        }
        
        // Keep the lock file until the next request (will be automatically released)
    }
} 