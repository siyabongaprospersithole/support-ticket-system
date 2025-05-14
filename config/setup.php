<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Prevent Environment Reloading
    |--------------------------------------------------------------------------
    |
    | This option determines whether Laravel should reload the environment
    | when .env file changes. During setup, this is set to true to
    | prevent constant reloading that would disrupt the setup process.
    |
    */
    'prevent_env_reload' => file_exists(storage_path('app/prevent_env_reload')),
    
    /*
    |--------------------------------------------------------------------------
    | Setup Lock File Timeout
    |--------------------------------------------------------------------------
    |
    | The number of seconds a setup lock file should be considered valid before
    | being automatically expired. This prevents stale locks from persisting.
    |
    */
    'lock_timeout' => 5,
]; 