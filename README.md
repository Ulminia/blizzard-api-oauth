blizzard-api-oauth
==================

Version 1.6

WORKS ONLY WITH NEW DEV PORTAL


at THIS TIME ONLY CODED FOR WOW USAGE


 
#    Setting up the client

require_once('Client.php');

#   Required vars for the api to work
```
$client_id = '';
$client_secret = '';
// see https://develop.battle.net/documentation/guides/regionality-and-apis for these settings
$region = 'US';
// al https://develop.battle.net/documentation/guides/regionality-and-apis for avail locals
$locale = 'en_US';
$redirect_uri = '';

// init the auth system client_id, client_secret, region, local all required
$client = new Client($client_id, $client_secret, $region, $locale, $redirect_uri);
```
#    Getting a token from the user to access there account

```
if (!isset($_GET['code']))
{
	$auth_url = $api->getAuthenticationUrl($api->baseurl[$api->region]['AUTHORIZATION_ENDPOINT'], $api->redirect_uri);
	echo '<script> location.replace("'.$auth_url.'"); </script>';
	exit();
}

$params = array('code' => $_GET['code'], 'auth_flow' => 'auth_code', 'grant_type' => 'authorization_code', 'redirect_uri' => $api->redirect_uri.$page);

$response = $api->getAccessToken($api->baseurl[$api->region]['TOKEN_ENDPOINT'], 'authorization_code', $params);
$api->setAccessToken($response['access_token']);```
