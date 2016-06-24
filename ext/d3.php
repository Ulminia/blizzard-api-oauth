<?php
/*
	this is the new api class for warcraft only wow related calls are used in it
*/


class d3 {
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

			case 'career_profile': 
				$q = '/d3/profile/'.$fields['battletag'].'/';
			case 'hero_profile': 
				$q = '/d3/profile/'.$fields['battletag'].'/hero/'.$fields['id'];

			/*
			data resources
			*/

			case 'item_data': 
				$q = '/d3/data/item/'.$fields['data'];
			case 'follower data': 
				$q = '/d3/data/follower/'.$fields['follower'];
			case 'artisan data': 
				$q = '/d3/data/artisan/'.$fields['artisan'];

			/*
			d3 data
			*/

			case 'season_index': 
				$q = '/data/d3/season/';
			case 'season': 
				$q = '/data/d3/season/'.$fields['id'];
			case 'season_leaderboard': 
				$q = '/data/d3/season/'.$fields['id'].'/leaderboard/'.$fields['leaderboard'];
			case 'era_index': 
				$q = '/data/d3/era/';
			case 'era': 
				$q = '/data/d3/era/'.$fields['id'];
			case 'era_leaderboard': 
				$q = '/data/d3/era/'.$fields['id'].'/leaderboard/'.$fields['leaderboard'];


			default:
			break;
		}
		//$q = str_replace('+' , '%20' , urlencode($q));
		return $q;
	}
	
}



?>