<?php
// Composer based autoloading (see: http://getcomposer.org/doc/04-schema.md#autoload)
require __DIR__ . '/vendor/autoload.php';

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
