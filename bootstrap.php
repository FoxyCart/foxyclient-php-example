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
 */
$config = array(
    'use_sandbox' => true
    );

/**
 * Uncomment this if you want to register FoxyClient directly.
 * An example sandbox client is given below with a client_full_access scoped token.
 */
//$config['client_id'] = 'client_39KFJ83nyeDAQxxxLPCb';
//$config['client_secret'] = 'H5QUsuHMjRmcV2LvZIMpRgPyHgZj1tqCs6sNuZrD';
//$config['refresh_token'] = 'ce245fbc1d955ed320dfa27ccbfce90a5c2d8e07';
/**
 * If you happen to store the current access_token and time it has left before needing to be refreshed, you can pass that in also.
 */
//$config['access_token'] = '818ea84b63a8873c9169ed62e88b6e8e6c200024';
//$config['access_token_expires'] = '1428097039';

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
