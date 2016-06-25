<?php
/*
	this is the new api class for warcraft only wow related calls are used in it
	version 1.4
*/


class sc {
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
			profile api
			*/

			case 'profile': 
				$q = '/sc2/profile/'.$fields['id'].'/'.$fields['region'].'/'.$fields['name'].'/';
				break;
			case 'ladders': 
				$q = '/sc2/profile/'.$fields['id'].'/'.$fields['region'].'/'.$fields['name'].'/ladders';
				break;
			case 'match_history': 
				$q = '/sc2/profile/'.$fields['id'].'/'.$fields['region'].'/'.$fields['name'].'/matches';
				break;

			/*
			ladder api
			*/

			case 'ladder': 
				$q = '/sc2/ladder/'.$fields['id'];
				break;

			/*
			data resources
			*/

			case 'achievements': 
				$q = '/sc2/data/achievements';
				break;
			case 'rewards': 
				$q = '/sc2/data/rewards';
				break;


			default:
			break;
		}
		//$q = str_replace('+' , '%20' , urlencode($q));
		return $q;
	}
	
}



?>