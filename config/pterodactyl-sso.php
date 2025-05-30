<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pterodactyl SSO Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Pterodactyl SSO extension.
    | The JWT secret should be a random string and must match the
    | APP_SSO_JWT_SECRET in your Pterodactyl .env file.
    |
    */
    
    'jwt_secret' => env('PTERODACTYL_SSO_JWT_SECRET', function () {
        // Generate a new secret
        $secret = bin2hex(random_bytes(32));
        
        // Get the .env file path
        $envFile = base_path('.env');
        
        // If .env exists, try to update it
        if (file_exists($envFile)) {
            $env = file_get_contents($envFile);
            
            // Check if the key already exists
            if (strpos($env, 'PTERODACTYL_SSO_JWT_SECRET') === false) {
                // Add new key
                file_put_contents($envFile, "\nPTERODACTYL_SSO_JWT_SECRET=$secret\n", FILE_APPEND);
            } else {
                // Update existing key
                $env = preg_replace(
                    '/^PTERODACTYL_SSO_JWT_SECRET=.*/m',
                    "PTERODACTYL_SSO_JWT_SECRET=$secret",
                    $env
                );
                file_put_contents($envFile, $env);
            }
        }
        
        return $secret;
    }()),
];
