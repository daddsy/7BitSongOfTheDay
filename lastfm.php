<?php
// Include the API
	
	
	class SongListing{
		private $title = '';
		private $artist = '';
		private $album = '';
		private $count = 0;
		
		public function setDetails($aTitle, $anArtist, $anAlbum){
			$this->title = $aTitle;
			$this->artist = $anArtist;
			$this->album = $anAlbum;
			$this->count = 1;
		}
		
		public function increaseCount(){
			$this->count++;
		}
		
		public function getTitle(){
			return $this->title;
		}
		
		public function getArtist(){
			return $this->artist;
		}
		
		public function getAlbum(){
			return $this->album;
		}
		
		public function getCount(){
			return $this->count;
		}
	
	}
	
	//sort declaration for song class
	function countSort($a, $b)
		{
			if ($a->getCount() == $b->getCount())
				{
					return 0;
				}
			return ($a->getCount() > $b->getCount()) ? -1 : 1;
		}
	
	require 'lfm/lastfmapi/lastfmapi.php';
	
	// Set the API key
	$authVars['apiKey'] = 'd85634ae0a6a71a028eeb4e3d0b1939b';
	// Pass the apiKey to the auth class to get a none fullAuth auth class
	$auth = new lastfmApiAuth('setsession', $authVars);
	
	//create a new API instance for user
	$apiClass = new lastfmApi();
	$userClass = $apiClass->getPackage($auth, 'user');
	
	//create a list of users to get records for
	$users = array(
		'sevenbitarcade',
		'shredzilla',
		'lyakali',
		'hibsta9',
		'tmbrad',
		'rhianstone',
		'micster',
		'djggou',
		'yamstersg',
		'daddsy'
	);
	
	$masterTracks = Array();
	
	//for every user
	foreach ( $users as $user ) {
	//return 100 recent tracks
		$methodVars = array(
			'user' => $user,
			'limit' => 100
		);
		$tracks = $userClass->getRecentTracks($methodVars);
	
		//iterate through the tracks
		foreach ($tracks as $track){
			//check if the scrobble occured in the last 24 hours
			if ($track['date'] > strtotime('-1 Day', time())){
			//flag to see if new instance is needed
			$found = false;
				foreach($masterTracks as $mTrack){
				//check whether it is already in the main array
					if (($mTrack->getTitle() == $track['name'])&&($mTrack->getArtist() == $track['artist']['name'])){
						//if it is then increase the counter on that item
						$mTrack->increaseCount();
						//set the flag as found
						$found = true;
					}
				}
				//if it isn't found then create it and add it to the array
				if ($found == false){
					$newSong = new SongListing();
					$newSong->setDetails($track['name'],$track['artist']['name'],$track['album']);
					array_push($masterTracks,$newSong);
				}
			}
		}
	
		
	}
	//reorder the array by most played
	
	uSort($masterTracks, "countSort");
	
	
	
	echo '<div><p>';
		foreach ($masterTracks as $mTrack){
			echo 'Artist: '.$mTrack->getArtist(). ', Track: '.$mTrack->getTitle().', Count: '.$mTrack->getCount();
			echo '<br />';
		}
	echo '</p></div>';
	
	
?>
