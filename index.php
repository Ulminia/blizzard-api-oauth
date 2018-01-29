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
$client = new OAuth2\Client($client_id, $client_secret, $region, $locale, $redirect_uri);


if (!isset($_GET['code']))
{
	$auth_url = $client->getAuthenticationUrl($client->baseurl[$client->region]['AUTHORIZATION_ENDPOINT'], $client->redirect_uri);
	//echo $auth_url;
	header('Location: ' . $auth_url);
}
else
{
	$params = array('code' => $_GET['code'], 'auth_flow' => 'auth_code', 'redirect_uri' => $client->redirect_uri);
	$response = $client->getAccessToken($client->baseurl[$client->region]['TOKEN_ENDPOINT'], 'authorization_code', $params);
echo '<pre>';
print_r($response);
echo '</pre>';
	$client->setAccessToken($response['result']['access_token']);
	$response = $client->fetch('user',array('source'=>'account'));
	echo '<pre>';
	print_r($response);
	echo '</pre>';
}
