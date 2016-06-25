blizzard-api-oauth
==================

	version 1.4
	
This application will use the blizzard api with oauth setting more info to come

How to use

Please See The Wiki... it explanes all the new calls.....

To make a Secure call the script cal be called as so

	if (!isset($_GET['code']))
	{

		$auth_url = $client->getAuthenticationUrl($client->baseurl[$client->region]['AUTHORIZATION_ENDPOINT'], $client->redirect_uri);
		header('Location: ' . $auth_url);
		die('Redirect');
	}
	else
	{
		$params = array('code' => $_GET['code'], 'auth_flow' => 'auth_code', 'redirect_uri' => $client->redirect_uri);
		$response = $client->getAccessToken($client->baseurl[$client->region]['TOKEN_ENDPOINT'], 'authorization_code', $params);
		$client->setAccessToken($response['result']['access_token']);
		$response = $client->fetch('user',array('source'=>'account'));
		echo '<pre>';
		print_r($response);
		echo '</pre>';
	}