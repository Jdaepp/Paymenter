<?php

namespace Paymenter\Extensions\Others\PterodactylSSO;

use App\Classes\Extension\Extension;
use Illuminate\Support\Facades\View;

class PterodactylSSO extends Extension
{
    /**
     * The extension's service provider.
     *
     * @var string
     */
    protected $provider = 'Paymenter\\Extensions\\Others\\PterodactylSSO\\PterodactylSSOServiceProvider';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Add the dashboard button
        $this->addDashboardButton();
    }
    
    /**
     * Add a button to the dashboard
     * 
     * @return void
     */
    protected function addDashboardButton()
    {
        // Add the button to the dashboard
        View::composer('templates.dashboard', function ($view) {
            $view->push('content', view('pterodactyl-sso::dashboard-button'));
        });
    }

    /**
     * Get all the configuration for the extension
     *
     * @param array $values Current configuration values
     * @return array
     */
    public function getConfig($values = [])
    {

        return [
            [
                'name' => 'sharedsecret',
                'label' => 'JWT Secret',
                'type' => 'text',
                'default' => '32-64 characters long string',
                'description' => 'Make a random string and copy it to your Pterodactyl panel\'s .env file as APP_SSO_JWT_SECRET',
                'validation' => 'required|string|min:32|max:64',
                'required' => true,
            ],
            [
                'name' => 'pterodactyl_url',
                'label' => 'Pterodactyl URL',
                'type' => 'text',
                'default' => '',
                'placeholder' => 'https://panel.yourdomain.com',
                'description' => 'The base URL of your Pterodactyl panel',
                'validation' => 'required|url',
                'required' => true,
            ],
            [
                'name' => 'token_expires_in',
                'label' => 'Token Expiration (seconds)',
                'type' => 'text',
                'default' => 60,
                'description' => 'How long the JWT token should be valid (in seconds)',
                'validation' => 'required|numeric|min:30|max:3600',
                'required' => true,
            ],
        ];
    }

    /**
     * Get a configuration value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function config($key, $default = null)
    {
        return parent::config($key, $default);
    }
}
