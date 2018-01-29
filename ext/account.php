<?php
/*
	this is the new api class for warcraft only wow related calls are used in it
	version 1.4
*/


class account {
	/**
	*	Type of call uri build
	*	$class - type of call
	*	$fields - array of data (name,server,size)
	**/
	public function _buildtype($class,$fields)
	{
		switch ($class)
		{
			/*
			account api
			*/

			case 'account':
				$q = 'account/user';
				break;
			case 'accountid':
				$q = 'account/user/id';
				break;
			case 'battletag':
				$q = 'account/user/battletag';
				break;

			/*
			profile api
			*/

			case 'sc2_oauth_profile': 
				$q = '/sc2/profile/user';
				break;
			case 'wow_oauth_profile': 
				$q = '/wow/user/characters';
				break;

			default:
			break;
		}
		return $q;
	}
	
}



?>