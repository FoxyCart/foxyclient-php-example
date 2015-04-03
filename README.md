# FoxyClient PHP Example
An example implementation of the FoxyClient for PHP

Please see <a href="https://api.foxycart.com/docs">the documentation</a> for more information.

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
 - Check if a Foxy user exists.
 - Create a Foxy user.
 - Check if a Foxy store exists.
 - Create a Foxy store.

 It also simplifies OAuth Interactions:
 - Authenticate client
 - Client Credentials grant (in case you forget your client_full_access refresh token)
 - Authorization Code grant (to get access to a user or store)
