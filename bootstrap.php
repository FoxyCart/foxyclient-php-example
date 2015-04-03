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
 * By default, use the sandbox.
 * You can also optionally pass in a access_token
 */
$config = array(
    'use_sandbox' => true
    );

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
