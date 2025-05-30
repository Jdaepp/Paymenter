<?php

namespace Paymenter\Extensions\Others\PterodactylSSO\Http\Controllers;

use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PterodactylSSOController extends Controller
{
    /**
     * Get the Pterodactyl API client
     * 
     * @return \Illuminate\Http\Client\PendingRequest
     * @throws \RuntimeException If Pterodactyl extension is not configured
     */
    protected function getPterodactylClient()
    {
        $pterodactylExtension = extension('Pterodactyl');
        
        if (!$pterodactylExtension) {
            throw new \RuntimeException('Pterodactyl extension is not installed or enabled');
        }
        
        $apiKey = $pterodactylExtension->config('api_key');
        $host = rtrim($pterodactylExtension->config('host'), '/');
        
        if (empty($apiKey) || empty($host)) {
            throw new \RuntimeException('Pterodactyl API key or host is not configured');
        }
        
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])
        ->timeout(10)
        ->retry(2, 100)
        ->baseUrl($host);
    }

    /**
     * Get user details from Pterodactyl by email
     * 
     * @param string $email
     * @return array|null
     */
    protected function getPterodactylUser($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Invalid email format when fetching Pterodactyl user', ['email' => $email]);
            return null;
        }

        $cacheKey = 'pterodactyl_user_' . md5($email);
        
        return Cache::remember($cacheKey, now()->addHour(), function () use ($email) {
            try {
                Log::debug('Fetching Pterodactyl user', ['email' => $email]);
                
                $response = $this->getPterodactylClient()
                    ->get('/api/application/users', [
                        'filter' => ['email' => $email],
                        'per_page' => 1
                    ]);

                if ($response->successful()) {
                    $data = $response->json('data');
                    if (!empty($data)) {
                        Log::debug('Found Pterodactyl user', [
                            'email' => $email, 
                            'username' => $data[0]['attributes']['username'] ?? null
                        ]);
                        return $data[0]['attributes'];
                    }
                    Log::debug('No Pterodactyl user found', ['email' => $email]);
                } else {
                    Log::error('Failed to fetch Pterodactyl user', [
                        'email' => $email,
                        'status' => $response->status(),
                        'response' => $response->json()
                    ]);
                }
                
                return null;
            } catch (RequestException $e) {
                Log::error('Pterodactyl API request failed', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return null;
            }
        });
    }

    /**
     * Redirect to Pterodactyl with JWT token
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        try {
            $user = Auth::user();
            $extension = extension('PterodactylSSO');
            
            // Validate configuration
            $pterodactylUrl = rtrim($extension->config('host'), '/');
            $jwtSecret = $extension->config('jwt_secret');
            $tokenExpiresIn = (int) $extension->config('token_expires_in', 60);
            
            if (empty($pterodactylUrl) || empty($jwtSecret)) {
                throw new \RuntimeException('Pterodactyl SSO is not properly configured. Please contact support.');
            }
            
            // Get the user's username from Pterodactyl
            $pterodactylUser = $this->getPterodactylUser($user->email);
            
            if (!$pterodactylUser) {
                throw new \RuntimeException('Your account was not found in Pterodactyl. Please contact support.');
            }
            
            // Generate JWT token
            $token = JWT::encode([
                'iss' => config('app.url'),
                'iat' => time(),
                'exp' => time() + $tokenExpiresIn,
                'data' => [
                    'email' => $user->email,
                    'username' => $pterodactylUser['username'],
                    'name' => $user->name,
                    'first_name' => $user->first_name ?? explode(' ', $user->name)[0] ?? 'User',
                    'last_name' => $user->last_name ?? (explode(' ', $user->name)[1] ?? ''),
                ]
            ], $jwtSecret, 'HS256');
            
            // Build redirect URL
            $redirectUrl = sprintf('%s/auth/sso?token=%s', $pterodactylUrl, $token);
            
            Log::info('Pterodactyl SSO redirect', [
                'user_id' => $user->id,
                'pterodactyl_user_id' => $pterodactylUser['id'] ?? null,
                'redirect_url' => $redirectUrl
            ]);
            
            // Redirect to Pterodactyl with the token
            return redirect()->away($redirectUrl);
            
        } catch (\Exception $e) {
            Log::error('Pterodactyl SSO Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to connect to Pterodactyl: ' . $e->getMessage());
        }
    }
}
