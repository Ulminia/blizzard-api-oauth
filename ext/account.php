<?php
/*
	this is the new api class for warcraft only wow related calls are used in it
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

			case 'user': 
				$q = '/account/user';

			/*
			profile api
			*/

			case 'sc2_oauth_profile': 
				$q = '/sc2/profile/user';
			case 'wow_oauth_profile': 
				$q = '/wow/user/characters';

			default:
			break;
		}
		//$q = str_replace('+' , '%20' , urlencode($q));
		return $q;
	}
	
}



?>