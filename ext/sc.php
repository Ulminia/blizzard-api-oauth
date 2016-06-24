<?php
/*
	this is the new api class for warcraft only wow related calls are used in it
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
			case 'ladders': 
				$q = '/sc2/profile/'.$fields['id'].'/'.$fields['region'].'/'.$fields['name'].'/ladders';
			case 'match_history': 
				$q = '/sc2/profile/'.$fields['id'].'/'.$fields['region'].'/'.$fields['name'].'/matches';

			/*
			ladder api
			*/

			case 'ladder': 
				$q = '/sc2/ladder/'.$fields['id'];

			/*
			data resources
			*/

			case 'achievements': 
				$q = '/sc2/data/achievements';
			case 'rewards': 
				$q = '/sc2/data/rewards';


			default:
			break;
		}
		//$q = str_replace('+' , '%20' , urlencode($q));
		return $q;
	}
	
}



?>