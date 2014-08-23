blizzard-api-oauth
==================

This application will use the blizzard api with oauth setting more info to come

How to use

	Include the required files
		require('Client.php');
		require('GrantType/IGrantType.php');
		require('GrantType/AuthorizationCode.php');

	Set the vars listed here to your oauth settings
	
		$client_id			= '';
		$client_secret		= '';
		$region				= 'US';		// Region of the call to be made too us, eu, sea ect
		$locale				= 'en_US';	// Locale for the output to be in
		$redirect_uri		= '';

Then Init the oauth class lib

	$client = new OAuth2\Client($client_id, $client_secret, $region, $locale, $redirect_uri);

Calls then can be made like so
	
	$r = $client->fetch('character',array('name'=>'ulminia','server'=>'zangarmarsh','fields'=>'items,stats'));
	
	
Acceptiable types for fetch calls are

achievement
					wow/achievement/$fields['name'];

auction
					wow/auction/data/$fields['server'];

abilities
					wow/battlepet/ability/$fields['name'];

species
					wow/battlepet/species/$fields['name'];

stats
					wow/battlepet/stats/$fields['name'];

realm_leaderboard
					wow/challenge/$fields['server'];

region_leaderboard
					wow/challenge/region

team
					wow/arena/$fields['server']/$fields['size']/$fields['name'];

character
					wow/character/$fields['server']/$fields['name'];

item
					wow/item/$fields['name'];

item_set
					wow/item/set/$fields['name'];

guild
					wow/guild/$fields['server']/$fields['name'];

leaderboards
					wow/leaderboard/$fields['size'];

quest
					wow/quest/$fields['name'];

realmstatus
					wow/realm/status

recipe
					wow/recipe/$fields['name'];

spell
					wow/spell/$fields['name'];

battlegroups
					wow/data/battlegroups/

races
					wow/data/character/races

classes
					wow/data/character/classes

achievements
					wow/data/character/achievements

guild_rewards
					wow/data/guild/rewards

guild_perks
					wow/data/guild/perks

guild_achievements
					wow/data/guild/achievements

item_classes
					wow/data/item/classes

talents
					wow/data/talents

pet_types
					wow/data/pet/types
**  These REQUIRE access_token to be set **
sc2profile
					sc2/profile/user
wowprofile
					wow/user/characters
accountid
					account/user/id
battletag
					account/user/battletag

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
		$response = $client->fetch('wowprofile');
		echo '<pre>';
		print_r($response);
		echo '</pre>';
	}