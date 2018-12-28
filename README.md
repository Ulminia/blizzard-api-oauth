blizzard-api-oauth
==================

Version 1.5

WORKS ONLY WITH NEW DEV PORTAL


at THIS TIME ONLY CODED FOR WOW USAGE

SOURCE NO LONGER NEEDED 

ACCESS TOKEN TYPE MUST NOW BE SET

$bob = $client->getAccessToken($client->baseurl[$client->->region]['TOKEN_ENDPOINT'], 'client_credentials', $parameters);
$client->setAccessToken($bob['access_token']);
$client->setAccessTokenType(1);
	