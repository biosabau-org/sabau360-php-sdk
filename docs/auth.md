# Sabau360 Auth Center PHP SDK

The **Sabau360 Auth Center PHP SDK** is a lightweight client library that simplifies authentication and authorization using the OpenID Connect (OIDC) standard with the Sabau360 Authorization Center.
It provides a straightforward interface for integrating secure login flows into your PHP applications.

## Features

- Fully compatible with OpenID Connect (OIDC) and OAuth 2.0.

- Simplified authentication flow using the Sabau360 Auth Center.

- Built-in token validation and decoding.

- Supports Access Token, ID Token, and Refresh Token handling.

- Easy to integrate into any PHP project using Composer.

## Installation

[Use Composer to install the SDK](../README.md)

## InstallationIntegration Guide

### Step 1: Redirect to Authorization Page

Create a file called `login.php`:

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Generate secure random values for state and nonce
$nonce = bin2hex(random_bytes(16));
$state = bin2hex(random_bytes(16));

$_SESSION['oauth2state'] = $state;
$_SESSION['oidc_nonce'] = $nonce;

// Initialize the SDK client
$client = new \Sabau360\SDK\Auth\Client();
$client->redirect_uri = 'https://myapp.net/callback.php'; // Must be registered in Sabau360 Auth Center
$client->state = $state;
$client->nonce = $nonce;

// Generate the authorization URL
$authUrl = $client->getAuthorizationUrl();

// Redirect the user to the Sabau360 Auth Center login page
header('Location: ' . $authUrl);
exit;

```
This script:

- Generates secure random state and nonce values to prevent replay attacks.

- Initializes the SDK client.

- Redirects the user to the Sabau360 Auth Center authorization endpoint.

### Step 2: Handle Callback and Retrieve Tokens

Create a file called `callback.php`:
```php
<?php

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Re-initialize the SDK client
$client = new \Sabau360\SDK\Auth\Client();
$client->state = $_SESSION['oauth2state'];
$client->nonce = $_SESSION['oidc_nonce'];

// Retrieve the authorization code and state from the callback
$token = $client->getToken($_GET['code'] ?? '', $_GET['state'] ?? '');

// Display token information
$result = [
    'access_token' => $token->getAccessToken(true),
    'id_token' => $token->getIdToken(true),
    'refresh_token' => $token->getRefreshToken(),
    'expires_at' => date('r', $token->getExpiresAt()),
    'remember' => $token->getRemember() ? 'yes' : 'no',
    'valid' => $token->isValid() ? 'yes' : 'no',
    'decoded_access_token' => $token->getAccessToken(false),
    'decoded_id_token' => $token->getIdToken(false),
];

echo "<pre>";
print_r($result);
echo "</pre>";
```
This script:

- Validates the authorization response from Sabau360.

- Exchanges the authorization code for tokens.

- Verifies and decodes the received tokens.

## Security Notes

- Always store state and nonce in the session before starting the login process.

- Use HTTPS in production environments.

- Never expose your client secret or token data to the browser.

- Always validate tokens before using them to access protected resources.




## Example Integration Flow

- User clicks “Login with Sabau360”.

- Browser is redirected to Sabau360 Auth Center.

- User logs in and authorizes your application.

- Sabau360 redirects back to your callback.php.

- The SDK exchanges the code for tokens.

- Your application validates and uses the tokens for authentication.