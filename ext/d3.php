<?php
/*
	this is the new api class for warcraft only wow related calls are used in it
	version 1.4
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
				break;
			case 'hero_profile': 
				$q = '/d3/profile/'.$fields['battletag'].'/hero/'.$fields['id'];
				break;

			/*
			data resources
			*/

			case 'item_data': 
				$q = '/d3/data/item/'.$fields['data'];
				break;
			case 'follower data': 
				$q = '/d3/data/follower/'.$fields['follower'];
				break;
			case 'artisan data': 
				$q = '/d3/data/artisan/'.$fields['artisan'];
				break;

			/*
			d3 data
			*/

			case 'season_index': 
				$q = '/data/d3/season/';
				break;
			case 'season': 
				$q = '/data/d3/season/'.$fields['id'];
				break;
			case 'season_leaderboard': 
				$q = '/data/d3/season/'.$fields['id'].'/leaderboard/'.$fields['leaderboard'];
				break;
			case 'era_index': 
				$q = '/data/d3/era/';
				break;
			case 'era': 
				$q = '/data/d3/era/'.$fields['id'];
				break;
			case 'era_leaderboard': 
				$q = '/data/d3/era/'.$fields['id'].'/leaderboard/'.$fields['leaderboard'];
				break;


			default:
			break;
		}
		//$q = str_replace('+' , '%20' , urlencode($q));
		return $q;
	}
	
}



?>