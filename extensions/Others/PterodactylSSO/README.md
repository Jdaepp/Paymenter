# Pterodactyl SSO Extension for Paymenter

This extension provides Single Sign-On (SSO) functionality for Pterodactyl Panel, allowing users to seamlessly access their Pterodactyl servers directly from Paymenter.

## Features

- One-click login to Pterodactyl Panel
- Secure JWT-based authentication
- Automatic user lookup and session creation
- Caching for improved performance
- Rate limiting to prevent abuse
- Comprehensive error handling and logging

## Requirements

- Paymenter 1.0.0 or higher
- Pterodactyl Panel 1.0.0 or higher
- Pterodactyl Server extension for Paymenter (for user provisioning)

## Installation

1. Copy the `PterodactylSSO` folder to `extensions/Others/`
2. Go to Admin → Extensions in your Paymenter admin panel
3. Find "Pterodactyl SSO" and click "Install"
4. Configure the extension with your Pterodactyl details

## Configuration

### Paymenter Configuration

1. Go to Admin → Extensions → Pterodactyl SSO → Configure
2. Set the following options:
   - **Enable Pterodactyl SSO**: Enable/disable the SSO functionality
   - **Pterodactyl URL**: The base URL of your Pterodactyl panel (e.g., https://panel.example.com)
   - **JWT Secret**: Must match `APP_SSO_JWT_SECRET` in your Pterodactyl `.env` file
   - **Button Text**: Text to display on the dashboard button (default: "Manage Server")
   - **Button Icon**: Font Awesome icon class for the button (default: "fa-server")

### Pterodactyl Configuration

1. Edit your Pterodactyl `.env` file and add:
   ```
   APP_SSO_JWT_SECRET=your-secure-jwt-secret-here
   ```
   
   Make sure this matches the JWT Secret in Paymenter.

2. Ensure your Pterodactyl panel is accessible from the internet and the URL matches what you configured.

## How It Works

1. When a user purchases a Pterodactyl server, the Pterodactyl extension creates their account in Pterodactyl.
2. The user sees a "Manage Server" button on their Paymenter dashboard.
3. When clicked, the extension:
   - Verifies the user's session
   - Looks up their Pterodactyl account by email
   - Generates a secure JWT token
   - Redirects them to Pterodactyl with the token
4. Pterodactyl validates the token and logs the user in automatically.

## Troubleshooting

### User Not Found
- Ensure the user exists in Pterodactyl (check the Pterodactyl admin panel)
- Verify the email addresses match between Paymenter and Pterodactyl
- Check the logs for any API errors

### Invalid Token
- Ensure the JWT secret matches exactly between Paymenter and Pterodactyl
- Check that the token isn't expiring too quickly
- Verify server times are synchronized

### API Connection Issues
- Check that the Pterodactyl panel is accessible from your Paymenter server
- Verify the API key in the Pterodactyl extension is correct and has admin permissions
- Check the logs for detailed error messages

## Security Considerations

- Always use HTTPS for both Paymenter and Pterodactyl
- Keep your JWT secret secure and never commit it to version control
- Regularly rotate your JWT secret
- Monitor the logs for any suspicious activity

## License

This extension is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
