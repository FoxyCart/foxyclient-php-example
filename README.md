# FoxyClient PHP Example
An example implementation of the FoxyClient for PHP

Please see <a href="https://api.foxycart.com/docs">the Foxy Hypermedia API documentation</a> for more information.

This is an example usage of <a href="https://github.com/FoxyCart/foxyclient-php">FoxyClient PHP</a>.

## See Also

Along with this example code, we're also be adding to the <a href="https://github.com/FoxyCart/foxyclient-php-playground">playground</a> for more examples of how to use the client. It currently demonstrates how to work with coupons: https://github.com/FoxyCart/foxyclient-php-playground

## Installation
Assume you're going to make the world a better place by improving software and fork the <a href="https://github.com/FoxyCart/foxyclient-php-example">foxyclient-php-example</a> repo.

Clone the forked repo on your computer:

`cd <where you want the foxyclient-php-example folder to live>`

`git clone git@github.com:<yourgithub-user-name>/foxyclient-php-example.git`

`cd foxyclient-php-example`

Install and run <a href="https://getcomposer.org/">composer</a>

`php composer.phar install`

## Usage

Start up a local PHP web server:

`php -S localhost:8000`

Load it up in your browser and follow along in the code:
<a href="http://localhost:8000">http://localhost:8000</a>

This example currently demonstrates how to:
 - Register your application by creating an OAuth client.
    - This creates an OAuth client record in our system which lets us know a bit about your integration. You'll get a client_id and client_secret which you need to save for future use, along with the access_token and refresh_token with the client_full_access scope so you can modify your OAuth client record later.
 - Check if a Foxy user exists.
 - Create a Foxy user.
 - Check if a Foxy store exists.
 - Create a Foxy store.

When authenticated to a store you can:
 - Create, modify, delete, and view Coupons and Coupon Codes
 - Create, modify, delete, and view Item Categories

It also simplifies OAuth Interactions:
 - Authenticate client
    - If you already have a client_id and client_secret you can authenticate this session using those credentials here. Also, ideally, if you have an OAuth refresh_token with the store_full_access scope, you can include that here as well to connect to your store.
 - Client Credentials grant
    - If you want to use the client_id and client_secret to get the client_full_access scoped refresh token for modifying your client (in case you forget your client_full_access refresh token).
 - Authorization Code grant
    - If you have a client_id and client_secret and you want to get access to your store or user.
