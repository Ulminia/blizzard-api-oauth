<?php
/**
*
*	version 1.4
*/
error_reporting(E_ALL);
 ini_set('display_errors', 1);


require_once('Client.php');
require_once('GrantType/IGrantType.php');
require_once('GrantType/AuthorizationCode.php');

/**
*	Required vars for the api to work
*
**/

$client_id			= '';
$client_secret		= '';
$region				= 'US';
$locale				= 'en_US';
$redirect_uri		= '';

// init the auth system client_id, client_secret, region, local all required
$client = new OAuth\oauthApi($client_id, $client_secret, $region, $locale, $redirect_uri);