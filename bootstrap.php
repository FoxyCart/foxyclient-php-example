<?php
// Composer based autoloading (see: http://getcomposer.org/doc/04-schema.md#autoload)
require __DIR__ . '/vendor/autoload.php';
date_default_timezone_set('America/Los_Angeles');
session_start();

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use Foxy\FoxyClient\FoxyClient;

/******* CONFIGURATION ********/
/**
 * If you want to toy around in the sandbox, change this to true.
 */
$config = array(
    'use_sandbox' => false
    );

/**
 * If you have already registered an OAuth client, you can add the client_id and client_secret below and uncomment these lines.
 * This will keep your client logged in all the time. Otherwise, you can select the "Authenticate Client" link and add
 * your values there to authenticate your client for this session only.
 */
//$config['client_id'] = '';
//$config['client_secret'] = '';

/**
 * If you already have an OAuth refresh token, add it below. Keep in mind, each token has a different scope such as
 *    client_full_accesss (allowing you to modify your OAuth client and create a user)
 *    user_full_accesss (allowing you to modify your user and create a store)
 *    store_full_access (allowing you to modify your store and create new stores)
 *
 * Ideally, for this client example, you want to paste in refresh token that has the store_full_access scope for a store.
 */
//$config['refresh_token'] = '';

/**
 * If you happen to have the current access_token and time it has left before needing to be refreshed, you can pass that in also, otherwise a new token will automatically be obtained.
 */
//$config['access_token'] = '';
//$config['access_token_expires'] = '';

$guzzle_config = array(
    'defaults' => array(
        'debug' => false,
        'exceptions' => false
        )
    );

/**
 * Set up our Guzzle Client
 */
$guzzle = new Client($guzzle_config);
CacheSubscriber::attach($guzzle);

/**
 * Get our FoxyClient
 */
$fc = new FoxyClient($guzzle, $config);

/**
 * Get some Cross Site Request Forgery protection love goin' on.
 */
$csrf = new \Riimu\Kit\CSRF\CSRFHandler(false);
try {
    $csrf->validateRequest(true);
} catch (\Riimu\Kit\CSRF\InvalidCSRFTokenException $ex) {
    header('HTTP/1.0 400 Bad Request');
    exit('Bad CSRF Token!');
}
$token = $csrf->getToken();
