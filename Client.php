<?php
/**
 * Note : Code is released under the GNU LGPL
 *
 * Please do not change the header of this file
 *
 * This library is free software; you can redistribute it and/or modify it under the terms of the GNU
 * Lesser General Public License as published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * See the GNU Lesser General Public License for more details.
 */

/**
 * Based off Light PHP wrapper for the OAuth 2.0 protocol.
 * Version 1.6
 *
 * @author      Joe Foster (Ulminia) <ulminia@gmail.com>

 */

require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'GrantType/IGrantType.php');
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'GrantType/AuthorizationCode.php');
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'GrantType/ClientCredentials.php');


class Client
{
    /**
     * Different AUTH method
     */
    const AUTH_TYPE_URI                 = 0;
    const AUTH_TYPE_AUTHORIZATION_BASIC = 1;
    const AUTH_TYPE_FORM                = 2;

    /**
     * Different Access token type
     */
    const ACCESS_TOKEN_URI      = 0;
    const ACCESS_TOKEN_BEARER   = 1;
    const ACCESS_TOKEN_OAUTH    = 2;
    const ACCESS_TOKEN_MAC      = 3;

    /**
    * Different Grant types
    */
    const GRANT_TYPE_AUTH_CODE          = 'authorization_code';
    const GRANT_TYPE_PASSWORD           = 'password';
    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_id';
    const GRANT_TYPE_REFRESH_TOKEN      = 'refresh_token';
	const GRANT_TYPE_C_C 				= 'client_credentials';
	
	const INVALID_GRANT_TYPE			= 'bob';

    /**
     * HTTP Methods
     */
    const HTTP_METHOD_GET    = 'GET';
    const HTTP_METHOD_POST   = 'POST';
    const HTTP_METHOD_PUT    = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_HEAD   = 'HEAD';
    const HTTP_METHOD_PATCH  = 'PATCH';

    /**
     * HTTP Form content types
     */
    const HTTP_FORM_CONTENT_TYPE_APPLICATION = 0;
    const HTTP_FORM_CONTENT_TYPE_MULTIPART = 1;

    /**
     * Client ID
     *
     * @var string
     */
    protected $client_id = null;

    /**
     * Client Secret
     *
     * @var string
     */
    protected $client_secret = null;

    /**
     * Client Authentication method
     *
     * @var int
     */
    protected $client_auth = self::AUTH_TYPE_URI;

    /**
     * Access Token
     *
     * @var string
     */
    public $access_token = null;

    /**
     * Access Token Type
     *
     * @var int
     */
    protected $access_token_type = self::ACCESS_TOKEN_URI;

    /**
     * Access Token Secret
     *
     * @var string
     */
    protected $access_token_secret = null;

    /**
     * Access Token crypt algorithm
     *
     * @var string
     */
    protected $access_token_algorithm = null;

    /**
     * Access Token Parameter name
     *
     * @var string
     */
    protected $access_token_param_name = 'access_token';

    /**
     * The path to the certificate file to use for https connections
     *
     * @var string  Defaults to .
     */
    protected $certificate_file = null;

    /**
     * cURL options
     *
     * @var array
     */
    protected $curl_options = array();

	/**
	 *	Redirect uri
	 *
	 */
	public $redirect_uri = '';
	
	
	/**
	 *	Base url setting
	 *
	 */
	public $baseurl = array(

			'US' => array(
				'urlbase'					=> 'https://us.api.blizzard.com',
				'AUTHORIZATION_ENDPOINT'	=> 'https://oauth.battle.net/authorize',
				'TOKEN_ENDPOINT'			=> 'https://oauth.battle.net/token',
				'ACCOUNT_ENDPOINT'			=> 'https://us.battle.net',
			),
			'EU' => array(
				'urlbase'					=> 'https://eu.api.blizzard.com',
				'AUTHORIZATION_ENDPOINT'	=> 'https://oauth.battle.net/authorize',
				'TOKEN_ENDPOINT'			=> 'https://oauth.battle.net/token',
				'ACCOUNT_ENDPOINT'			=> 'https://eu.battle.net',
			),
			'KR' => array(
				'urlbase'					=> 'https://kr.api.blizzard.com',
				'AUTHORIZATION_ENDPOINT'	=> 'https://oauth.battle.net/authorize',
				'TOKEN_ENDPOINT'			=> 'https://oauth.battle.net/token',
				'ACCOUNT_ENDPOINT'			=> 'https://kr.battle.net',
			),
			'TW' => array(
				'urlbase'					=> 'https://tw.api.blizzard.com',
				'AUTHORIZATION_ENDPOINT'	=> 'https://oauth.battle.net/authorize',
				'TOKEN_ENDPOINT'			=> 'https://oauth.battle.net/token',
				'ACCOUNT_ENDPOINT'			=> 'https://tw.battle.net',
			),
			'CN' => array(
				'urlbase'					=> 'https://gateway.battlenet.com.cn',
				'AUTHORIZATION_ENDPOINT'	=> 'https://oauth.battle.net.cn/authorize',
				'TOKEN_ENDPOINT'			=> 'https://oauth.battle.net.cn/token',
				'ACCOUNT_ENDPOINT'			=> 'https://cn.battle.net',
			),
			'SEA' => array(
				'urlbase'					=> 'https://sea.api.blizzard.com',
				'AUTHORIZATION_ENDPOINT'	=> 'https://oauth.battle.net/authorize',
				'TOKEN_ENDPOINT'			=> 'https://oauth.battle.net/token',
				'ACCOUNT_ENDPOINT'			=> 'https://sea.battle.net',
			),
	);
	
	/**
	 *	region setting
	 *
	 */
	public $region = '';
	
	 /**
	 *	Locale setting
	 *
	 */
	public $locale = '';
	
	/*
	*	some tracking bits for people
	*/
	public $usage = array(
				'type'				=> '',
				'url'				=> '',
				'responce_code'		=> '',
				'content_type'		=> '',
				'locale'			=> '',
			);
	
	public $errno = CURLE_OK;
	public $error = '';
    /**
     * Construct
     *
     * @param string $client_id Client ID
     * @param string $client_secret Client Secret
     * @param int    $client_auth (AUTH_TYPE_URI, AUTH_TYPE_AUTHORIZATION_BASIC, AUTH_TYPE_FORM)
     * @param string $certificate_file Indicates if we want to use a certificate file to trust the server. Optional, defaults to null.
     * @return void
     */
    public function __construct($client_id, $client_secret, $region, $locale, $redirect_uri)
    {
        if (!extension_loaded('curl')) {
            throw new Exception('The PHP exention curl must be installed to use this library.', Exception::CURL_NOT_FOUND);
        }
		
		$r = preg_replace('/http:/', 'https:', $redirect_uri);
		$client_auth			= self::AUTH_TYPE_URI;
        $this->client_id		= $client_id;
        $this->client_secret	= $client_secret;
		$this->region			= $region;
		$this->locale			= $locale;
        $this->client_auth		= $client_auth;
		$this->redirect_uri		= $r;
		$bob = $this->getAccessToken($this->baseurl[$this->region]['TOKEN_ENDPOINT'], 'client_credentials',array());

		if ( isset($bob['access_token']) )
		{
			$this->setAccessToken($bob['access_token']);
			$this->setAccessTokenType(1);
		}
	
    }

	public function set_region($region)
	{
		$this->region = $region;
	}
	
    /**
     * Get the client Id
     *
     * @return string Client ID
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * Get the client Secret
     *
     * @return string Client Secret
     */
    public function getClientSecret()
    {
        return $this->client_secret;
    }

    /**
     * getAuthenticationUrl
     *
     * @param string $auth_endpoint Url of the authentication endpoint
     * @param string $redirect_uri  Redirection URI
     * @param array  $extra_parameters  Array of extra parameters like scope or state (Ex: array('scope' => null, 'state' => ''))
     * @return string URL used for authentication
     */
    public function getAuthenticationUrl($auth_endpoint, $redirect_uri, array $extra_parameters = array())
    {
        $parameters = array_merge(array(
            'response_type' => 'code',
            'client_id'     => $this->client_id,
			'scope'			=> 'wow.profile',
			'auth_flow'		=> 'auth_code',
            'redirect_uri'  => $redirect_uri
        ), $extra_parameters);
        return $auth_endpoint . '?' . http_build_query($parameters, null, '&');
    }

    /**
     * getAccessToken
     *
     * @param string $token_endpoint    Url of the token endpoint
     * @param int    $grant_type        Grant Type ('authorization_code', 'password', 'client_credentials', 'refresh_token', or a custom code (@see GrantType Classes)
     * @param array  $parameters        Array sent to the server (depend on which grant type you're using)
     * @return array Array of parameters required by the grant_type (CF SPEC)
     */
    public function getAccessToken($token_endpoint, $grant_type, array $parameters)
    {
        if (!$grant_type) {
            throw new InvalidArgumentException('The grant_type is mandatory.', InvalidArgumentException::INVALID_GRANT_TYPE);
        }
        $grantTypeClassName = $this->convertToCamelCase($grant_type);
        $grantTypeClass = $grantTypeClassName;
        if (!class_exists($grantTypeClass)) {
            throw new InvalidArgumentException('Unknown grant type \'' . $grant_type . '\' ['.$grantTypeClass.']', InvalidArgumentException::INVALID_GRANT_TYPE);
        }
        $grantTypeObject = new $grantTypeClass();
        $grantTypeObject->validateParameters($parameters);
        if (!defined($grantTypeClass . '::GRANT_TYPE')) {
            throw new Exception('Unknown constant GRANT_TYPE for class ' . $grantTypeClassName, Exception::GRANT_TYPE_ERROR);
        }
        $parameters['grant_type'] = $grantTypeClass::GRANT_TYPE;
        $http_headers = array();
        switch ($this->client_auth) {
            case self::AUTH_TYPE_URI:
            case self::AUTH_TYPE_FORM:
                $parameters['client_id'] = $this->client_id;
                $parameters['client_secret'] = $this->client_secret;
                break;
            case self::AUTH_TYPE_AUTHORIZATION_BASIC:
                $parameters['client_id'] = $this->client_id;
                $http_headers['Authorization'] = 'Basic ' . base64_encode($this->client_id .  ':' . $this->client_secret);
                break;
            default:
                throw new Exception('Unknown client auth type.', Exception::INVALID_CLIENT_AUTHENTICATION_TYPE);
                break;
        }

        $result = $this->executeRequest($token_endpoint, $parameters, self::HTTP_METHOD_POST, $http_headers, self::HTTP_FORM_CONTENT_TYPE_APPLICATION);

		return $result;
    }

    /**
     * setToken
     *
     * @param string $token Set the access token
     * @return void
     */
    public function setAccessToken($token)
    {
        $this->access_token = $token;
    }

    /**
     * Set the client authentication type
     *
     * @param string $client_auth (AUTH_TYPE_URI, AUTH_TYPE_AUTHORIZATION_BASIC, AUTH_TYPE_FORM)
     * @return void
     */
    public function setClientAuthType($client_auth)
    {
        $this->client_auth = $client_auth;
    }

    /**
     * Set an option for the curl transfer
     *
     * @param int   $option The CURLOPT_XXX option to set
     * @param mixed $value  The value to be set on option
     * @return void
     */
    public function setCurlOption($option, $value)
    {
        $this->curl_options[$option] = $value;
    }

    /**
     * Set multiple options for a cURL transfer
     *
     * @param array $options An array specifying which options to set and their values
     * @return void
     */
    public function setCurlOptions($options) 
    {
        $this->curl_options = array_merge($this->curl_options, $options);
    }

    /**
     * Set the access token type
     *
     * @param int $type Access token type (ACCESS_TOKEN_BEARER, ACCESS_TOKEN_MAC, ACCESS_TOKEN_URI)
     * @param string $secret The secret key used to encrypt the MAC header
     * @param string $algorithm Algorithm used to encrypt the signature
     * @return void
     */
    public function setAccessTokenType($type, $secret = null, $algorithm = null)
    {
        $this->access_token_type = $type;
        $this->access_token_secret = $secret;
        $this->access_token_algorithm = $algorithm;
    }

	
	protected function _buildUrl($path, $params = array())
    {
		// allways called in all api calls
		$params['apikey'] = $this->client_id;
		if (isset($this->access_token))
		{
			$params['access_token']	= $this->access_token;
		}
		//set for translation
		$params['locale'] = $this->locale;

		if ( !isset($params['namespace']) )
		{
			$params['namespace'] = 'static-'.strtolower($this->region);
		}
		if ($path == 'account')
		{
			$url = $this->baseurl[$this->region]['ACCOUNT_ENDPOINT'];
		}
		else
		{				
			$url = $this->baseurl[$this->region]['urlbase'];
		}
		//$url .= $path;
		$url .= self::_buildtype($path,$params);
		unset($params['name']);
		unset($params['server']);
		$url .= (count($params)) ? '?' . $this->_build_strings($params, '&') : '';
		$this->usage = array (
			'type'		=> $path,
			'url'		=> $url,
			'locale'	=> $this->locale
		);
		//echo $url;
		return $url;
		
    }
	
	function _build_strings($params, $sep)
	{
		$e = '';
		$r = array();
		foreach($params as $key=>$val)
		{
			$r[] = $key.'='.$val;
		}
		$e = implode($sep, $r);
		return $e;
	}
	
	/**
	*	Type of call uri build
	*	$class - type of call
	*	$fields - array of data (name,server,size)
	**/
	public function _buildtype($classa,$fields)
	{
		if ( isset($fields['server']) )
		{
			$fields['realm'] = $fields['server'];
		}
		
		$class = str_replace('-', '_', $classa);
		
		switch ($class)
		{
			/*
				User API
			*/
			case 'wowprofile':
				$q = '/wow/user/characters';
				$s = '/wow/user/characters';
			break;
			case 'account':
				$q = '/oauth/userinfo';
				$s = '/oauth/userinfo';
			break;
			
			/*
				Account Profile API
			*/
			case 'account_profile_summary':
				$q = '/profile/user/wow';
				$s = '/profile/user/wow';
			break;
			case 'protected_character_profile_summary':
				$q = '/profile/user/wow/protected-character/'.$fields['realmId'].'-'.$fields['characterId'].'';
				$s = '/profile/user/wow/protected-character/{realmId}-{characterId}';
			break;
			case 'account_collections_index':
				$q = '/profile/user/wow/collections';
				$s = '/profile/user/wow/collections';
			break;
			case 'account_mounts_collection_summary':
				$q = '/profile/user/wow/collections/mounts';
				$s = '/profile/user/wow/collections/mounts';
			break;
			case 'account_pets_collection_summary':
				$q = '/profile/user/wow/collections/pets';
				$s = '/profile/user/wow/collections/pets';
			break;


			/*
			*	wow game data api's
			*
			*
			*/

			/*  Achievement API */

			case 'achievement_categories_index':
				$q = '/data/wow/achievement-category/index';
				$s = '/data/wow/achievement-category/index';
			break;

			case 'achievement_category':
				$q = '/data/wow/achievement-category/'.$fields['achievementCategoryId'];
				$s = '/data/wow/achievement-category/{achievementCategoryId}';
			break;

			case 'achievements_index':
				$q = '/data/wow/achievement/index';
				$s = '/data/wow/achievement/index';
			break;

			case 'achievement':
				$q = '/data/wow/achievement/'.$fields['achievementId'];
				$s = '/data/wow/achievement/{achievementId}';
			break;

			case 'achievement_media':
				$q = '/data/wow/media/achievement/'.$fields['achievementId'];
				$s = '/data/wow/media/achievement/{achievementId}';
			break;


			/*  Auction House API */

			case 'auctions':
				$q = '/data/wow/connected-realm/'.$fields['connectedRealmId'].'/auctions';
				$s = '/data/wow/connected-realm/{connectedRealmId}/auctions';
			break;


			/*  Azerite Essence API */

			case 'azerite_essences_index':
				$q = '/data/wow/azerite-essence/index';
				$s = '/data/wow/azerite-essence/index';
			break;

			case 'azerite_essence':
				$q = '/data/wow/azerite-essence/'.$fields['azeriteEssenceId'];
				$s = '/data/wow/azerite-essence/{azeriteEssenceId}';
			break;

			case 'azerite_essence_search':
				$q = '/data/wow/search/azerite-essence';
				$s = '/data/wow/search/azerite-essence';
			break;

			case 'azerite_essence_media':
				$q = '/data/wow/media/azerite-essence/'.$fields['azeriteEssenceId'];
				$s = '/data/wow/media/azerite-essence/{azeriteEssenceId}';
			break;

			/*  Connected Realm API */

			case 'connected_realms_index':
				$q = '/data/wow/connected-realm/index';
				$s = '/data/wow/connected-realm/index';
			break;

			case 'connected_realm':
				$q = '/data/wow/connected-realm/'.$fields['connectedRealmId'];
				$s = '/data/wow/connected-realm/{connectedRealmId}';
			break;

			case 'connected_realms_search':
				$q = '/data/wow/search/connected-realm';
				$s = '/data/wow/search/connected-realm';
			break;

			/*  Covenant API */

			case 'covenant_index':
				$q = '/data/wow/covenant/index';
				$s = '/data/wow/covenant/index';
			break;

			case 'covenant':
				$q = '/data/wow/covenant/'.$fields['covenantId'];
				$s = '/data/wow/covenant/{covenantId}';
			break;

			case 'covenant_media':
				$q = '/data/wow/media/covenant/'.$fields['covenantId'];
				$s = '/data/wow/media/covenant/{covenantId}';
			break;

			case 'soulbind_index':
				$q = '/data/wow/covenant/soulbind/index';
				$s = '/data/wow/covenant/soulbind/index';
			break;

			case 'soulbind':
				$q = '/data/wow/covenant/soulbind/'.$fields['soulbindId'];
				$s = '/data/wow/covenant/soulbind/{soulbindId}';
			break;

			case 'conduit_index':
				$q = '/data/wow/covenant/conduit/index';
				$s = '/data/wow/covenant/conduit/index';
			break;

			case 'conduit':
				$q = '/data/wow/covenant/conduit/'.$fields['conduitId'];
				$s = '/data/wow/covenant/conduit/{conduitId}';
			break;




			/*  Creature API */

			case 'creature_families_index':
				$q = '/data/wow/creature-family/index';
				$s = '/data/wow/creature-family/index';
			break;

			case 'creature_family':
				$q = '/data/wow/creature-family/'.$fields['creatureFamilyId'];
				$s = '/data/wow/creature-family/{creatureFamilyId}';
			break;

			case 'creature_types_index':
				$q = '/data/wow/creature-type/index';
				$s = '/data/wow/creature-type/index';
			break;

			case 'creature_type':
				$q = '/data/wow/creature-type/'.$fields['creatureTypeId'];
				$s = '/data/wow/creature-type/{creatureTypeId}';
			break;

			case 'creature':
				$q = '/data/wow/creature/'.$fields['creatureId'];
				$s = '/data/wow/creature/{creatureId}';
			break;

			case 'creature_search':
				$q = '/data/wow/search/creature';
				$s = '/data/wow/search/creature';
			break;

			case 'creature_display_media':
				$q = '/data/wow/media/creature-display/'.$fields['creatureDisplayId'];
				$s = '/data/wow/media/creature-display/{creatureDisplayId}';
			break;

			case 'creature_family_media':
				$q = '/data/wow/media/creature-family/'.$fields['creatureFamilyId'];
				$s = '/data/wow/media/creature-family/{creatureFamilyId}';
			break;


			/*  Guild Crest API */

			case 'guild_crest_components_index':
				$q = '/data/wow/guild-crest/index';
				$s = '/data/wow/guild-crest/index';
			break;

			case 'guild_crest_border_media':
				$q = '/data/wow/media/guild-crest/border/'.$fields['borderId'];
				$s = '/data/wow/media/guild-crest/border/{borderId}';
			break;

			case 'guild_crest_emblem_media':
				$q = '/data/wow/media/guild-crest/emblem/'.$fields['emblemId'];
				$s = '/data/wow/media/guild-crest/emblem/{emblemId}';
			break;


			/*  Item API */

			case 'item_classes_index':
				$q = '/data/wow/item-class/index';
				$s = '/data/wow/item-class/index';
			break;

			case 'item_class':
				$q = '/data/wow/item-class/'.$fields['itemClassId'];
				$s = '/data/wow/item-class/{itemClassId}';
			break;

			case 'item_sets_index':
				$q = '/data/wow/item-set/index';
				$s = '/data/wow/item-set/index';
			break;

			case 'item_set':
				$q = '/data/wow/item-set/'.$fields['itemSetId'];
				$s = '/data/wow/item-set/{itemSetId}';
			break;

			case 'item_subclass':
				$q = '/data/wow/item-class/'.$fields['itemClassId'].'/item-subclass/'.$fields['itemSubclassId'];
				$s = '/data/wow/item-class/{itemClassId}/item-subclass/{itemSubclassId}';
			break;

			case 'item':
				$q = '/data/wow/item/'.$fields['itemId'];
				$s = '/data/wow/item/{itemId}';
			break;

			case 'item_media':
				$q = '/data/wow/media/item/'.$fields['itemId'];
				$s = '/data/wow/media/item/{itemId}';
			break;

			case 'item_search':
				$q = '/data/wow/search/item';
				$s = '/data/wow/search/item';
			break;


			/*  Journal API */

			case 'journal_expansions_index':
				$q = '/data/wow/journal-expansion/index';
				$s = '/data/wow/journal-expansion/index';
			break;

			case 'journal_expansion':
				$q = '/data/wow/journal-expansion/'.$fields['journalExpansionId'];
				$s = '/data/wow/journal-expansion/{journalExpansionId}';
			break;

			case 'journal_encounters_index':
				$q = '/data/wow/journal-encounter/index';
				$s = '/data/wow/journal-encounter/index';
			break;

			case 'journal_encounter':
				$q = '/data/wow/journal-encounter/'.$fields['journalEncounterId'];
				$s = '/data/wow/journal-encounter/{journalEncounterId}';
			break;

			case 'journal_encounter_search':
				$q = '/data/wow/search/journal-encounter';
				$s = '/data/wow/search/journal-encounter';
			break;

			case 'journal_instances_index':
				$q = '/data/wow/journal-instance/index';
				$s = '/data/wow/journal-instance/index';
			break;

			case 'journal_instance':
				$q = '/data/wow/journal-instance/'.$fields['journalInstanceId'];
				$s = '/data/wow/journal-instance/{journalInstanceId}';
			break;

			case 'journal_instance_media':
				$q = '/data/wow/media/journal-instance/'.$fields['journalInstanceId'];
				$s = '/data/wow/media/journal-instance/{journalInstanceId}';
			break;


			/*  Media Search API */

			case 'media_search':
				$q = '/data/wow/search/media';
				$s = '/data/wow/search/media';
			break;


			/*  Modified Crafting API */

			case 'modified_crafting_index':
				$q = '/data/wow/modified-crafting/index';
				$s = '/data/wow/modified-crafting/index';
			break;


			case 'modified_crafting_category_index':
				$q = '/data/wow/modified-crafting/category/index';
				$s = '/data/wow/modified-crafting/category/index';
			break;


			case 'modified_crafting_category':
				$q = '/data/wow/modified-crafting/category/'.$fields['categoryId'];
				$s = '/data/wow/modified-crafting/category/{categoryId}';
			break;

			case 'modified-crafting-reagent-slot-type-index':
				$q = '/data/wow/modified-crafting/reagent-slot-type/index';
				$s = '/data/wow/modified-crafting/reagent-slot-type/index';
			break;

			case 'modified-crafting-reagent-slot-type':
				$q = '/data/wow/modified-crafting/reagent-slot-type/'.$fields['slotTypeId'];
				$s = '/data/wow/modified-crafting/reagent-slot-type/{slotTypeId}';
			break;


			/*  Mount API */

			case 'mounts_index':
				$q = '/data/wow/mount/index';
				$s = '/data/wow/mount/index';
			break;

			case 'mount':
				$q = '/data/wow/mount/'.$fields['mountId'];
				$s = '/data/wow/mount/{mountId}';
			break;

			case 'mount_search':
				$q = '/data/wow/search/mount';
				$s = '/data/wow/search/mount';
			break;


			/*  Mythic Keystone Affix API */

			case 'mythic_keystone_affixes_index': 
				$q = '/data/wow/keystone-affix/index';
				$s = '/data/wow/keystone-affix/index';
			break;

			case 'mythic_keystone_affix':
				$q = '/data/wow/keystone-affix/'.$fields['keystoneAffixId'];
				$s = '/data/wow/keystone-affix/{keystoneAffixId}';
			break;

			case 'mythic_keystone_affix_media':
				$q = '/data/wow/media/keystone-affix/'.$fields['keystoneAffixId'];
				$s = '/data/wow/media/keystone-affix/{keystoneAffixId}';
			break;


			/*  Mythic Keystone Dungeon API */

			case 'mythic_keystone_dungeons_index':
				$q = '/data/wow/mythic-keystone/dungeon/index';
				$s = '/data/wow/mythic-keystone/dungeon/index';
			break;

			case 'mythic_keystone_dungeon':
				$q = '/data/wow/mythic-keystone/dungeon/'.$fields['dungeonId'];
				$s = '/data/wow/mythic-keystone/dungeon/{dungeonId}';
			break;

			case 'mythic_keystone_index':
				$q = '/data/wow/mythic-keystone/index';
				$s = '/data/wow/mythic-keystone/index';
			break;

			case 'mythic_keystone_periods_index':
				$q = '/data/wow/mythic-keystone/period/index';
				$s = '/data/wow/mythic-keystone/period/index';
			break;

			case 'mythic_keystone_period':
				$q = '/data/wow/mythic-keystone/period/'.$fields['periodId'];
				$s = '/data/wow/mythic-keystone/period/{periodId}';
			break;

			case 'mythic_keystone_seasons_index':
				$q = '/data/wow/mythic-keystone/season/index';
				$s = '/data/wow/mythic-keystone/season/index';
			break;

			case 'mythic_keystone_season':
				$q = '/data/wow/mythic-keystone/season/'.$fields['seasonId'];
				$s = '/data/wow/mythic-keystone/season/{seasonId}';
			break;


			/*  Mythic Keystone Leaderboard API */

			case 'mythic_keystone_leaderboards_index':
				$q = '/data/wow/connected-realm/'.$fields['connectedRealmId'].'/mythic-leaderboard/index';
				$s = '/data/wow/connected-realm/{connectedRealmId}/mythic-leaderboard/index';
			break;

			case 'mythic_keystone_leaderboard':
				$q = '/data/wow/connected-realm/'.$fields['connectedRealmId'].'/mythic-leaderboard/'.$fields['dungeonId'].'/period/'.$fields['period'];
				$s = '/data/wow/connected-realm/{connectedRealmId}/mythic-leaderboard/{dungeonId}/period/{period}';
			break;


			/*  Mythic Raid Leaderboard API */

			case 'mythic_raid_leaderboard':
				$q = '/data/wow/leaderboard/hall-of-fame/'.$fields['raid'].'/'.$fields['faction'];
				$s = '/data/wow/leaderboard/hall-of-fame/{raid}/{faction}';
			break;


			/*  Pet API */

			case 'pets_index':
				$q = '/data/wow/pet/index';
				$s = '/data/wow/pet/index';
			break;

			case 'pet':
				$q = '/data/wow/pet/'.$fields['petId'];
				$s = '/data/wow/pet/{petId}';
			break;

			case 'pet_media':
				$q = '/data/wow/media/pet/'.$fields['petId'];
				$s = '/data/wow/media/pet/{petId}';
			break;

			case 'pet_abilities_index':
				$q = '/data/wow/pet-ability/index';
				$s = '/data/wow/pet-ability/index';
			break;

			case 'pet_ability':
				$q = '/data/wow/pet-ability/'.$fields['petAbilityId'];
				$s = '/data/wow/pet-ability/{petAbilityId}';
			break;

			case 'pet_ability_media':
				$q = '/data/wow/media/pet-ability/'.$fields['petAbilityId'];
				$s = '/data/wow/media/pet-ability/{petAbilityId}';
			break;


			/*  Playable Class API */

			case 'playable_classes_index':
				$q = '/data/wow/playable-class/index';
				$s = '/data/wow/playable-class/index';
			break;

			case 'playable_class':
				$q = '/data/wow/playable-class/'.$fields['classId'];
				$s = '/data/wow/playable-class/{classId}';
			break;

			case 'playable_class_media':
				$q = '/data/wow/media/playable-class/'.$fields['playableClassId'];
				$s = '/data/wow/media/playable-class/{playableClassId}';
			break;

			case 'pvp_talent_slots':
				$q = '/data/wow/playable-class/'.$fields['classId'].'/pvp-talent-slots';
				$s = '/data/wow/playable-class/{classId}/pvp-talent-slots';
			break;


			/*  Playable Race API */

			case 'playable_races_index':
				$q = '/data/wow/playable-race/index';
				$s = '/data/wow/playable-race/index';
			break;


			case 'playable_race':
				$q = '/data/wow/playable-race/'.$fields['playableRaceId'];
				$s = '/data/wow/playable-race/{playableRaceId}';


			/*  Playable Specialization API */

			case 'playable_specializations_index':
				$q = '/data/wow/playable-specialization/index';
				$s = '/data/wow/playable-specialization/index';
			break;

			case 'playable_specialization':
				$q = '/data/wow/playable-specialization/'.$fields['specId'];
				$s = '/data/wow/playable-specialization/{specId}';
			break;

			case 'playable_specialization_media':
				$q = '/data/wow/media/playable-specialization/'.$fields['specId'];
				$s = '/data/wow/media/playable-specialization/{specId}';
			break;


			/*  Power Type API */

			case 'power_types_index':
				$q = '/data/wow/power-type/index';
				$s = '/data/wow/power-type/index';
			break;

			case 'power_type':
				$q = '/data/wow/power-type/'.$fields['powerTypeId'];
				$s = '/data/wow/power-type/{powerTypeId}';
			break;


			/*  Profession API */

			case 'professions_index':
				$q = '/data/wow/profession/index';
				$s = '/data/wow/profession/index';
			break;

			case 'profession':
				$q = '/data/wow/profession/'.$fields['professionId'];
				$s = '/data/wow/profession/{professionId}';
			break;

			case 'profession_media':
				$q = '/data/wow/media/profession/'.$fields['professionId'];
				$s = '/data/wow/media/profession/{professionId}';
			break;

			case 'profession_skill_tier':
				$q = '/data/wow/profession/'.$fields['professionId'].'/skill-tier/'.$fields['skillTierId'];
				$s = '/data/wow/profession/{professionId}/skill-tier/{skillTierId}';
			break;

			case 'recipe':
				$q = '/data/wow/recipe/'.$fields['recipeId'];
				$s = '/data/wow/recipe/{recipeId}';
			break;

			case 'recipe_media':
				$q = '/data/wow/media/recipe/'.$fields['recipeId'];
				$s = '/data/wow/media/recipe/{recipeId}';
			break;


			/*  PvP Season API */

			case 'pvp_seasons_index':
				$q = '/data/wow/pvp-season/index';
				$s = '/data/wow/pvp-season/index';
			break;

			case 'pvp_season':
				$q = '/data/wow/pvp-season/'.$fields['pvpSeasonId'];
				$s = '/data/wow/pvp-season/{pvpSeasonId}';
			break;

			case 'pvp_leaderboards_index':
				$q = '/data/wow/pvp-season/'.$fields['pvpSeasonId'].'/pvp-leaderboard/index';
				$s = '/data/wow/pvp-season/{pvpSeasonId}/pvp-leaderboard/index';
			break;

			case 'pvp_leaderboard':
				$q = '/data/wow/pvp-season/'.$fields['pvpSeasonId'].'/pvp-leaderboard/'.$fields['pvpBracket'];
				$s = '/data/wow/pvp-season/{pvpSeasonId}/pvp-leaderboard/{pvpBracket}';
			break;

			case 'pvp_rewards_index':
				$q = '/data/wow/pvp-season/'.$fields['pvpSeasonId'].'/pvp-reward/index';
				$s = '/data/wow/pvp-season/{pvpSeasonId}/pvp-reward/index';
			break;


			/*  PvP Tier API */

			case 'pvp_tier_media':
				$q = '/data/wow/media/pvp-tier/'.$fields['pvpTierId'];
				$s = '/data/wow/media/pvp-tier/{pvpTierId}';

			break;


			case 'pvp_tiers_index':
				$q = '/data/wow/pvp-tier/index';
				$s = '/data/wow/pvp-tier/index';
			break;


			case 'pvp_tier':
				$q = '/data/wow/pvp-tier/'.$fields['pvpTierId'];
				$s = '/data/wow/pvp-tier/{pvpTierId}';
			break;


			/*  Quest API */

			case 'quests_index':
				$q = '/data/wow/quest/index';
				$s = '/data/wow/quest/index';
			break;

			case 'quest':
				$q = '/data/wow/quest/'.$fields['questId'];
				$s = '/data/wow/quest/{questId}';
			break;

			case 'quest_categories_index':
				$q = '/data/wow/quest/category/index';
				$s = '/data/wow/quest/category/index';
			break;

			case 'quest_category':
				$q = '/data/wow/quest/category/'.$fields['questCategoryId'];
				$s = '/data/wow/quest/category/{questCategoryId}';
			break;

			case 'quest_areas_index':
				$q = '/data/wow/quest/area/index';
				$s = '/data/wow/quest/area/index';
			break;

			case 'quest_area':
				$q = '/data/wow/quest/area/'.$fields['questAreaId'];
				$s = '/data/wow/quest/area/{questAreaId}';
			break;

			case 'quest_types_index':
				$q = '/data/wow/quest/type/index';
				$s = '/data/wow/quest/type/index';
			break;

			case 'quest_type':
				$q = '/data/wow/quest/type/'.$fields['questTypeId'];
				$s = '/data/wow/quest/type/{questTypeId}';
			break;


			/*  Realm API */

			case 'realms_index':
				$q = '/data/wow/realm/index';
				$s = '/data/wow/realm/index';
			break;

			case 'realm':
				$q = '/data/wow/realm/'.$fields['server'];
				$s = '/data/wow/realm/{server}';
			break;

			case 'realm_search':
				$q = '/data/wow/search/realm';
				$s = '/data/wow/search/realm';
			break;


			/*  Region API */

			case 'regions_index':
				$q = '/data/wow/region/index';
				$s = '/data/wow/region/index';
			break;

			case 'region':
				$q = '/data/wow/region/'.$fields['regionId'];
				$s = '/data/wow/region/{regionId}';
			break;


			/*  Reputations API */

			case 'reputation_factions_index':
				$q = '/data/wow/reputation-faction/index';
				$s = '/data/wow/reputation-faction/index';
			break;

			case 'reputation_faction':
				$q = '/data/wow/reputation-faction/'.$fields['reputationFactionId'];
				$s = '/data/wow/reputation-faction/{reputationFactionId}';
			break;

			case 'reputation_tiers_index':
				$q = '/data/wow/reputation-tiers/index';
				$s = '/data/wow/reputation-tiers/index';
			break;

			case 'reputation_tiers':
				$q = '/data/wow/reputation-tiers/'.$fields['reputationTiersId'];
				$s = '/data/wow/reputation-tiers/{reputationTiersId}';
			break;


			/*  Spell API */

			case 'spell':
				$q = '/data/wow/spell/'.$fields['spellId'];
				$s = '/data/wow/spell/{spellId}';
			break;

			case 'spell_media':
				$q = '/data/wow/media/spell/'.$fields['spellId'];
				$s = '/data/wow/media/spell/{spellId}';
			break;

			case 'spell_search':
				$q = '/data/wow/search/spell';
				$s = '/data/wow/search/spell';
			break;


			/*  Talent API */

			case 'talents_index':
				$q = '/data/wow/talent/index';
				$s = '/data/wow/talent/index';
			break;

			case 'talent':
				$q = '/data/wow/talent/'.$fields['talentId'];
				$s = '/data/wow/talent/{talentId}';
			break;

			case 'pvp_talents_index':
				$q = '/data/wow/pvp-talent/index';
				$s = '/data/wow/pvp-talent/index';
			break;

			case 'pvp_talent':
				$q = '/data/wow/pvp-talent/'.$fields['pvpTalentId'];
				$s = '/data/wow/pvp-talent/{pvpTalentId}';
			break;


			/*  Tech Talent API */

			case 'tech_talent_tree_index':
				$q = '/data/wow/tech-talent-tree/index';
				$s = '/data/wow/tech-talent-tree/index';
			break;

			case 'tech_talent_tree':
				$q = '/data/wow/tech-talent-tree/'.$fields['techTalentTreeId'];
				$s = '/data/wow/tech-talent-tree/{techTalentTreeId}';
			break;

			case 'tech_talent_index':
				$q = '/data/wow/tech-talent/index';
				$s = '/data/wow/tech-talent/index';
			break;

			case 'tech_talent':
				$q = '/data/wow/tech-talent/'.$fields['techTalentId'];
				$s = '/data/wow/tech-talent/{techTalentId}';
			break;

			case 'tech_talent_media':
				$q = '/data/wow/media/tech-talent/'.$fields['techTalentId'];
				$s = '/data/wow/media/tech-talent/{techTalentId}';
			break;


			/*  Title API */

			case 'titles_index':
				$q = '/data/wow/title/index';
				$s = '/data/wow/title/index';
			break;

			case 'title':
				$q = '/data/wow/title/'.$fields['titleId'];
				$s = '/data/wow/title/{titleId}';
			break;


			/*  WoW Token API */

			case 'wow_token_index':
				$q = '/data/wow/token/index';
				$s = '/data/wow/token/index';
			break;


			/*###############################################################################################################################
			*
			*
			*			Profile API Start
			*
			*
			*/###############################################################################################################################


			/*  Account Profile API */

			case 'account_profile_summary':
				$q = '/profile/user/wow';
				$s = '/profile/user/wow';
			break;

			case 'protected_character_profile_summary':
				$q = '/profile/user/wow/protected-character/'.$fields['realmId'].'-'.$fields['characterId'];
				$s = '/profile/user/wow/protected-character/{realmId}-{characterId}';
			break;

			case 'account_collections_index':
				$q = '/profile/user/wow/collections';
				$s = '/profile/user/wow/collections';
			break;

			case 'account_mounts_collection_summary':
				$q = '/profile/user/wow/collections/mounts';
				$s = '/profile/user/wow/collections/mounts';
			break;

			case 'account_pets_collection_summary':
				$q = '/profile/user/wow/collections/pets';
				$s = '/profile/user/wow/collections/pets';
			break;


			/*  Character Achievements API */

			case 'character_achievements_summary':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/achievements';
				$s = '/profile/wow/character/{server}/{name}/achievements';
			break;

			case 'character_achievement_statistics':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/achievements/statistics';
				$s = '/profile/wow/character/{server}/{name}/achievements/statistics';
			break;


			/*  Character Appearance API */

			case 'character_appearance_summary':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/appearance';
				$s = '/profile/wow/character/{server}/{name}/appearance';
			break;


			/*  Character Collections API */

			case 'character_collections':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/collections';
				$s = '/profile/wow/character/{server}/{name}/collections';
			break;

			case 'character_collections_mounts':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/collections/mounts';
				$s = '/profile/wow/character/{server}/{name}/collections/mounts';
			break;

			case 'character_collections_pets':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/collections/pets';
				$s = '/profile/wow/character/{server}/{name}/collections/pets';
			break;


			/*  Character Encounters API */

			case 'character_encounters_summary':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/encounters';
				$s = '/profile/wow/character/{server}/{name}/encounters';
			break;

			case 'character_dungeons':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/encounters/dungeons';
				$s = '/profile/wow/character/{server}/{name}/encounters/dungeons';
			break;

			case 'character_raids':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/encounters/raids';
				$s = '/profile/wow/character/{server}/{name}/encounters/raids';
			break;


			/*  Character Equipment API */

			case 'character_equipment_summary':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/equipment';
				$s = '/profile/wow/character/{server}/{name}/equipment';
			break;


			/*  Character Hunter Pets API */

			case 'character_hunter_pets_summary':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/hunter-pets';
				$s = '/profile/wow/character/{server}/{name}/hunter-pets';
			break;


			/*  Character Media API */

			case 'character_media_summary':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/character-media';
				$s = '/profile/wow/character/{server}/{name}/character-media';
			break;


			/*  Character Mythic Keystone Profile API */

			case 'character-mythic-keystone-profile-index':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/mythic-keystone-profile';
				$s = '/profile/wow/character/{server}/{name}/mythic-keystone-profile';
			break;

			case 'character-mythic-keystone-season-details':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/mythic-keystone-profile/season/'.$fields['seasonId'];
				$s = '/profile/wow/character/{server}/{name}/mythic-keystone-profile/season/{seasonId}';
			break;


			/*  Character Professions API */

			case 'character_professions':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/professions';
				$s = '/profile/wow/character/{server}/{name}/professions';
			break;


			/*  Character Profile API */

			case 'character_profile_summary':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8');
				$s = '/profile/wow/character/{server}/{name}';
			break;

			case 'character_profile_status':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/status';
				$s = '/profile/wow/character/{server}/{name}/status';
			break;


			/*  Character PvP API */

			case 'character_pvp_bracket_statistics':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/pvp-bracket/'.$fields['pvpBracket'];
				$s = '/profile/wow/character/{server}/{name}/pvp-bracket/{pvpBracket}';
			break;


			case 'character_pvp_summary':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/pvp-summary';
				$s = '/profile/wow/character/{server}/{name}/pvp-summary';
			break;


			/*  Character Quests API */

			case 'character_quests':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/quests';
				$s = '/profile/wow/character/{server}/{name}/quests';
			break;

			case 'character_completed_quests':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/quests/completed';
				$s = '/profile/wow/character/{server}/{name}/quests/completed';
			break;


			/*  Character Reputations API */

			case 'character_reputations_summary':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/reputations';
				$s = '/profile/wow/character/{server}/{name}/reputations';
			break;


			/*  Character Soulbinds API */

			case 'character_soulbinds':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/soulbinds';
				$s = '/profile/wow/character/{server}/{name}/soulbinds';
			break;


			/*  Character Specializations API */

			case 'character_specializations_summary':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/specializations';
				$s = '/profile/wow/character/{server}/{name}/specializations';
			break;


			/*  Character Statistics API */

			case 'character_statistics_summary':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/statistics';
				$s = '/profile/wow/character/{server}/{name}/statistics';
			break;


			/*  Character Titles API */

			case 'character_titles_summary':
				$q = '/profile/wow/character/'.$fields['server'].'/'.mb_strtolower($fields['name'], 'UTF-8').'/titles';
				$s = '/profile/wow/character/{server}/{name}/titles';
			break;


			/*  Guild API */

			case 'guild':
				$q = '/data/wow/guild/'.$fields['server'].'/'.$fields['nameSlug'];
				$s = '/data/wow/guild/{server}/{nameSlug}';
			break;

			case 'guild_activity':
				$q = '/data/wow/guild/'.$fields['server'].'/'.$fields['nameSlug'].'/activity';
				$s = '/data/wow/guild/{server}/{nameSlug}/activity';
			break;

			case 'guild_achievements':
				$q = '/data/wow/guild/'.$fields['server'].'/'.$fields['nameSlug'].'/achievements';
				$s = '/data/wow/guild/{server}/{nameSlug}/achievements';
			break;

			case 'guild_roster':
				$q = '/data/wow/guild/'.$fields['server'].'/'.$fields['nameSlug'].'/roster';
				$s = '/data/wow/guild/{server}/{nameSlug}/roster';
			break;

			
		}
		preg_match_all('/{([a-zA-Z0-9]*)}/', $s, $matches);
		//echo $s.'<br><pre>';print_r($matches);echo '</pre><br><br>';
		$cont = true;
		$fild = array();
		$missing = array();
		foreach($matches[1] as $i => $v)
		{
			$fild[] = $v;
			if ( !isset( $fields[$v] ) && empty( $fields[$v] ) )
			{
				$cont = false;
				$missing[] = $v;
			}
		}
		try
		{
			if (!$cont)
			{
				throw new EmptyField();
			}
			
		}
		catch (EmptyField $ex)
		{
			echo "<br>Missing paramiter(s) [ ".implode(' , ',$missing)." ] in api call<br>'".$class."' must be defined in [ ".$s." ]<br>Required Fields: [ ".implode(' , ',$fild)." ]<br>Fields sent: ( ".implode( ' , ', array_keys($fields) )." )<br>";
		}

		return $q;
	}	
    /**
     * Fetch a protected ressource
     *
     * @param string $protected_ressource_url Protected resource URL
     * @param array  $parameters Array of parameters
     * @param string $http_method HTTP Method to use (POST, PUT, GET, HEAD, DELETE)
     * @param array  $http_headers HTTP headers
     * @param int    $form_content_type HTTP form content type to use
     * @return array
     */
    public function fetch($protected_resource_url, $parameters = array(), $http_headers = array(), $http_method = self::HTTP_METHOD_GET, $form_content_type = self::HTTP_FORM_CONTENT_TYPE_MULTIPART)
    {
		global $roster;
		$lmh = array();
		if ( isset($http_headers['If-Modified-Since']) && $http_headers['If-Modified-Since'] != '')
		{
			$http_headers['If-Modified-Since'] = $http_headers['If-Modified-Since'];
			$lmh['Last-Modified'] = $http_headers['If-Modified-Since'];
		}
		else
		{
			//if no header info make the call as normal if there is a header apply it
			if (isset($lmh['Last-Modified']))
			{
				$roster->debug->_debug( 2, false, '['.$protected_resource_url.'] LMH EXISTS setting IF-MOD header', 'OK' );
				$http_headers['If-Modified-Since'] = gmdate('D, d M Y H:i:s \G\M\T',$lmh['Last-Modified']);
			}
		}
		
		$protected_resource_url = self::_buildUrl($protected_resource_url, $parameters);
		
        if ($this->access_token) {
            switch ($this->access_token_type) {
                case self::ACCESS_TOKEN_URI:
                    if (is_array($parameters)) {
                        $parameters[$this->access_token_param_name] = $this->access_token;
                    } else {
                        throw new InvalidArgumentException(
                            'You need to give parameters as array if you want to give the token within the URI.',
                            InvalidArgumentException::REQUIRE_PARAMS_AS_ARRAY
                        );
                    }
                    break;
                case self::ACCESS_TOKEN_BEARER:
                    $http_headers['Authorization'] = 'Bearer ' . $this->access_token;
                    break;
                case self::ACCESS_TOKEN_OAUTH:
                    $http_headers['Authorization'] = 'OAuth ' . $this->access_token;
                    break;
                case self::ACCESS_TOKEN_MAC:
                    $http_headers['Authorization'] = 'MAC ' . $this->generateMACSignature($protected_resource_url, $parameters, $http_method);
                    break;
                default:
                    throw new Exception('Unknown access token type.', Exception::INVALID_ACCESS_TOKEN_TYPE);
                    break;
            }
        }
		$http_headers['Accept-Encoding'] = 'gzip';

		$result = $this->executeRequest($protected_resource_url, $parameters, $http_method, $http_headers, $form_content_type);

		return $result;
    }

    /**
     * Generate the MAC signature
     *
     * @param string $url Called URL
     * @param array  $parameters Parameters
     * @param string $http_method Http Method
     * @return string
     */
    private function generateMACSignature($url, $parameters, $http_method)
    {
        $timestamp = time();
        $nonce = uniqid();
        $parsed_url = parse_url($url);
        if (!isset($parsed_url['port']))
        {
            $parsed_url['port'] = ($parsed_url['scheme'] == 'https') ? 443 : 80;
        }
        if ($http_method == self::HTTP_METHOD_GET) {
            if (is_array($parameters)) {
                $parsed_url['path'] .= '?' . http_build_query($parameters, null, '&');
            } elseif ($parameters) {
                $parsed_url['path'] .= '?' . $parameters;
            }
        }

        $signature = base64_encode(hash_hmac($this->access_token_algorithm,
                    $timestamp . "\n"
                    . $nonce . "\n"
                    . $http_method . "\n"
                    . $parsed_url['path'] . "\n"
                    . $parsed_url['host'] . "\n"
                    . $parsed_url['port'] . "\n\n"
                    , $this->access_token_secret, true));

        return 'id="' . $this->access_token . '", ts="' . $timestamp . '", nonce="' . $nonce . '", mac="' . $signature . '"';
    }

    /**
     * Execute a request (with curl)
     *
     * @param string $url URL
     * @param mixed  $parameters Array of parameters
     * @param string $http_method HTTP Method
     * @param array  $http_headers HTTP Headers
     * @param int    $form_content_type HTTP form content type to use
     * @return array
     */
    private function executeRequest($url, $parameters = array(), $http_method = self::HTTP_METHOD_GET, array $http_headers = null, $form_content_type = self::HTTP_FORM_CONTENT_TYPE_MULTIPART)
    {
		global $roster;
		
		//echo $url.'<br>'.$http_method.'<br>';
		/*
		echo '<pre>';
		print_r($parameters);
		$t = $parameters;
		print_r($http_headers);
		echo '</pre>';
		*/
		
        $curl_options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CUSTOMREQUEST  => $http_method
        );
        switch($http_method) {
            case self::HTTP_METHOD_POST:
                $curl_options[CURLOPT_POST] = true;
				
                /* No break */
            case self::HTTP_METHOD_PUT:
			case self::HTTP_METHOD_PATCH:

                /**
                 * Passing an array to CURLOPT_POSTFIELDS will encode the data as multipart/form-data,
                 * while passing a URL-encoded string will encode the data as application/x-www-form-urlencoded.
                 * http://php.net/manual/en/function.curl-setopt.php
                 */
                if(is_array($parameters) && self::HTTP_FORM_CONTENT_TYPE_APPLICATION === $form_content_type) {
                    $parameters = http_build_query($parameters, null, '&');
                }
                $curl_options[CURLOPT_POSTFIELDS] = $parameters;
                break;
            case self::HTTP_METHOD_HEAD:
                $curl_options[CURLOPT_NOBODY] = true;
                /* No break */
            case self::HTTP_METHOD_DELETE:
            case self::HTTP_METHOD_GET:
                
                break;
            default:
                break;
        }
		//echo $url.'<br>';
        $curl_options[CURLOPT_URL] = $url;
		$curl_options[CURLOPT_HEADER] = true;

		$http_headers['Accept-Encoding'] = 'gzip';
		if (isset($t['grant_type']) && $t['grant_type'] == 'client_credentials')
		{
			unset($http_headers['Accept-Encoding']);
		}

        if (is_array($http_headers)) {
            $header = array();
            foreach($http_headers as $key => $parsed_urlvalue) {
                $header[] = "$key: $parsed_urlvalue";
            }
            $curl_options[CURLOPT_HTTPHEADER] = $header;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $curl_options);
        // https handling
        if (!empty($this->curl_options))
		{
            curl_setopt_array($ch, $this->curl_options);
        }
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		if (!isset($t['grant_type']) )
		{
			curl_setopt($ch, CURLOPT_ENCODING , "gzip");
		}
        $result = curl_exec($ch);
		$header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
		$return_headres = $this->get_headers_from_curl_response(substr($result, 0, $header_size));
		
		$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		$this->errno	= curl_errno($ch);
		$this->error	= curl_error($ch);
		
		//d($result,$http_code,$this->errno,$this->error,$content_type);

		$this->usage['responce_code'] = $http_code;
		$this->usage['content_type'] = $content_type;

		
		
		
        if ($this->errno)
		{
			$json_decode = json_decode(substr( $result, $header_size ), true);
			$json_decode['header'] = $return_headres;//$this->get_headers_from_curl_response(substr($result, 0, $header_size));
			$json_decode['http_code'] = '~'.$http_code;
			$json_decode['last_url'] = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
			//curl_close($ch);
			 //( $parameters );
			//return $json_decode;
        }
		else
		{
			$header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
			$json_decode = json_decode(substr( $result, $header_size ), true);
			$json_decode['header'] = $return_headres;//$this->get_headers_from_curl_response(substr($result, 0, $header_size));
			$json_decode['http_code'] = $http_code;
			$json_decode['last_url'] = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
			
        }

		//if (isset($json_decode['status']) OR $json_decode['status'] == 'nok' OR 
		if ( $json_decode['http_code'] != 200 && $json_decode['http_code'] != 304 )
		{
			$header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
			$json_decode = json_decode(substr( $result, $header_size ), true);
			$json_decode['header'] = $return_headres;//$this->get_headers_from_curl_response(substr($result, 0, $header_size));
			$json_decode['http_code'] = $http_code;//curl_getinfo($ch,CURLINFO_HTTP_CODE);
			$json_decode['last_url'] = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
			
		}
		
        curl_close($ch);
		return (null === $json_decode) ? $result : $json_decode;
    }

	public function decode_gzip($h,$d,$rn="\r\n")
	{
		if (isset($h['transfer-encoding'])){
		$lrn = strlen($rn);
		$str = '';
		$ofs=0;
		do{
			$p = strpos($d,$rn,$ofs);
			$len = hexdec(substr($d,$ofs,$p-$ofs));
			$str .= substr($d,$p+$lrn,$len);
			 $ofs = $p+$lrn*2+$len;
		}while ($d[$ofs]!=='0');
		$d=$str;
		}
		if (isset($h['eontent-encoding'])) $d = gzinflate(substr($d,4));
		return $d;
	}

	public function get_headers_from_curl_response($response)
	{
		$headers = array();

		$header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

		foreach (explode("\r\n", $header_text) as $i => $line)
			if ($i === 0)
				$headers['http_code'] = $line;
			else
			{
				list ($key, $value) = explode(': ', $line);

				$headers[$key] = $value;
			}

		return $headers;
	}
    /**
     * Set the name of the parameter that carry the access token
     *
     * @param string $name Token parameter name
     * @return void
     */
    public function setAccessTokenParamName($name)
    {
        $this->access_token_param_name = $name;
    }

    /**
     * Converts the class name to camel case
     *
     * @param  mixed  $grant_type  the grant type
     * @return string
     */
    private function convertToCamelCase($grant_type)
    {
        $parts = explode('_', $grant_type);
        array_walk($parts, function(&$item) { $item = ucfirst($item);});
        return implode('', $parts);
    }
}
/*
class Exception extends Exception
{
    const CURL_NOT_FOUND                     = 0x01;
    const CURL_ERROR                         = 0x02;
    const GRANT_TYPE_ERROR                   = 0x03;
    const INVALID_CLIENT_AUTHENTICATION_TYPE = 0x04;
    const INVALID_ACCESS_TOKEN_TYPE          = 0x05;
}

class InvalidArgumentException extends InvalidArgumentException
{
    const INVALID_GRANT_TYPE      = 0x01;
    const CERTIFICATE_NOT_FOUND   = 0x02;
    const REQUIRE_PARAMS_AS_ARRAY = 0x03;
    const MISSING_PARAMETER       = 0x04;
}
*/