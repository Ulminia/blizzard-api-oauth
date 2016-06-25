<?php
/*
	this is the new api class for warcraft only wow related calls are used in it
	version 1.4
*/


class wow {
	/**
	*	Type of call uri build
	*	$class - type of call
	*	$fields - array of data (name,server,size)
	**/
	public function _buildtype($class,$fields)
	{
		switch ($class)
		{
			case 'achievement':
						$q = 'wow/achievement/'.$fields['id'];
					break;
			case 'auction':
						$q = 'wow/auction/data/'.$fields['server'];
					break;
			case 'abilities':
						$q = 'wow/battlepet/ability/'.$fields['id'];
					break;
			case 'species':
						$q = 'wow/battlepet/species/'.$fields['id'];
					break;
			case 'stats':
						$q = 'wow/battlepet/stats/'.$fields['id'];
					break;
			case 'realm_leaderboard':
						$q = 'wow/challenge/'.$fields['server'];
					break;
			case 'region_leaderboard':
						$q = 'wow/challenge/region';
					break;
			case 'team':
						$q = 'wow/arena/'.$fields['server'].'/'.$fields['size'].'/'.$fields['name'];
					break;
			case 'character':
						$q = 'wow/character/'.$fields['server'].'/'.$fields['name'];
					break;
			case 'item':
						$q = 'wow/item/'.$fields['id'];
					break;
			case 'item_set':
						$q = 'wow/item/set/'.$fields['id'];
					break;
			case 'guild':
						$q = 'wow/guild/'.$fields['server'].'/'.$fields['name'];
					break;
			case 'leaderboards':
						$q = 'wow/leaderboard/'.$fields['size'];
					break;
			case 'quest':
						$q = 'wow/quest/'.$fields['id'];
					break;
			case 'realmstatus':
						$q = 'wow/realm/status';
					break;
			case 'recipe':
						$q = 'wow/recipe/'.$fields['id'];
					break;
			case 'spell':
						$q = 'wow/spell/'.$fields['id'];
					break;
			case 'battlegroups':
						$q = 'wow/data/battlegroups/';
					break;
			case 'races':
						$q = 'wow/data/character/races';
					break;
			case 'classes':
						$q = 'wow/data/character/classes';
					break;
			case 'achievements':
						$q = 'wow/data/character/achievements';
					break;
			case 'guild_rewards':
						$q = 'wow/data/guild/rewards';
					break;
			case 'guild_perks':
						$q = 'wow/data/guild/perks';
					break;
			case 'guild_achievements':
						$q = 'wow/data/guild/achievements';
					break;
			case 'item_classes':
						$q = 'wow/data/item/classes';
					break;
			case 'talents':
						$q = 'wow/data/talents';
					break;
			case 'pet_types':
						$q = 'wow/data/pet/types';
					break;

			default:
			break;
		}
		//$q = str_replace('+' , '%20' , urlencode($q));
		return $q;
	}
	
}



?>