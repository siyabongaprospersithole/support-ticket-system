<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ValidateSetupState
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip validation for setup routes
        if ($request->is('setup*')) {
            return $next($request);
        }

        $setupStatePath = storage_path('app/setup_state.json');
        $setupCompletedPath = storage_path('app/setup_completed');

        try {
            // Check if setup state file exists and is valid
            if (!File::exists($setupStatePath)) {
                // If the old completed file exists but no state file, we need to re-run setup
                if (File::exists($setupCompletedPath)) {
                    File::delete($setupCompletedPath);
                }
                return redirect('/setup');
            }

            // Read and validate setup state
            $setupState = json_decode(File::get($setupStatePath), true);
            
            // Validate setup state structure
            if (!$this->isValidSetupState($setupState)) {
                Log::warning('Invalid setup state detected', ['state' => $setupState]);
                return redirect('/setup');
            }

            // Validate environment matches
            if ($setupState['environment'] !== app()->environment()) {
                Log::warning('Environment mismatch detected', [
                    'stored' => $setupState['environment'],
                    'current' => app()->environment()
                ]);
                return redirect('/setup');
            }

            // Additional validations can be added here
            // For example, checking database connection, mail settings, etc.

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Setup state validation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/setup');
        }
    }

    /**
     * Validate the setup state structure
     */
    private function isValidSetupState(?array $state): bool
    {
        if (!is_array($state)) {
            return false;
        }

        $requiredKeys = [
            'completed_at',
            'database_configured',
            'migrations_run',
            'mail_configured',
            'version',
            'php_version',
            'environment'
        ];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $state)) {
                return false;
            }
        }

        return true;
    }
} 