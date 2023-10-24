<?php
/**
*
*	version 1.6
*/
error_reporting(E_ALL);
 ini_set('display_errors', 1);


require_once('Client.php');
/**
*	Required vars for the api to work
*
**/

$client_id			= '';
$client_secret		= '';
$region				= 'US'; // see https://develop.battle.net/documentation/guides/regionality-and-apis for these settings
$locale				= 'en_US'; // al https://develop.battle.net/documentation/guides/regionality-and-apis for avail locals
$redirect_uri		= '';

// init the auth system client_id, client_secret, region, local all required
$client = new Client($client_id, $client_secret, $region, $locale, $redirect_uri);