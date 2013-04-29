<?php

// NO WARRANTY OR SUPPORT OF ANY KIND IS PROVIDED FOR THIS SOFTWARE.

require_once('../inc/include.php');

class Lookup {

	/**
	 * Gets the next film for human verification
	 */
	public static function next() {
		
		$db = new DB();

		// loop until we find a record that needs human verification
		while(true) {
			$record = $db->selectNextRecord();

			if(empty($record)) {
				return false;
			}

			// try to pull in details about the record from external services
			Lookup::populate($record);

			$catId = $record->getCatId();
			$tmdbId = $record->getTmdbId();
			$imdbId = $record->getImdbId();
			
			if(empty($tmdbId) && empty($imdbId)) {
				// if record not found in tmdb or imdb, mark as finished and move on
				$db->setRecordStatus($catId, 'no');
			} else if(empty($imdbId)) {
				// do not attempt to look at a record having a tmdbId but not an imdbId
				$db->insertResult($catId, $tmdbId, '', 'no');
			} else {
				// we found a record that needs human verification
				return $record;
			}
		}
	}
	
	/**
	 * Populates the film with a possible match in IMDb
	 */
	public static function populate($record) {
		
		if(empty($record)) {
			return $record;
		}

		$catId = $record->getCatId();
		$tmdbId = $record->getTmdbId();
		$imdbId = $record->getImdbId();
		$title = $record->getTitle();

		if(empty($catId)) {
			return $record;
		}

		if(empty($tmdbId)) {
			if(!empty($imdbId)) {
				$tmdbId = Lookup::getTmdbIdUsingImdbId($imdbId);
				$record->setTmdbId($tmdbId);
			} else {
				$tmdbId = Lookup::getTmdbId($catId, $title);
				$record->setTmdbId($tmdbId);
			}
		}

		if(empty($imdbId)) {
			$imdbId = Lookup::getImdbId($tmdbId);
			$record->setImdbId($imdbId);
		}
		
		return $record;

	}

	/**
	 * Find the next candidate given the title that has not already been human verified
	 */
	public static function getTmdbId($catId, $title) {

		if(empty($catId) || empty($title)) {
			return;
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://api.themoviedb.org/3/search/movie?api_key=" . urlencode(TMDB_API_KEY) . "&query=" . urlencode($title));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$json = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);

		if(!empty($error)) {
			Util::fatal("Cannot get tmdbId: " . $error);
		}
		
		$db = new DB();
		$checkedTmdbIds = $db->selectCheckedTmdbIds($catId);

		$data = json_decode($json);
		
		foreach($data->results as $result) {
			$tmdbId = $result->id;
			
			if(!in_array($tmdbId, $checkedTmdbIds) && !$result->adult) {
				return $tmdbId;
			}
		}
		
		return null;
	}

	/**
	 * Get the tmdbId given the imdbId
	 */
	public static function getTmdbIdUsingImdbId($imdbId) {

		if(empty($imdbId)) {
			return;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://api.themoviedb.org/3/movie/$imdbId?api_key=" . urlencode(TMDB_API_KEY));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$json = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);

		if(!empty($error)) {
			Util::fatal("Cannot get tmdbId: " . $error);
		}

		$data = json_decode($json);
		$tmdbId = isset($data->id) ? $data->id : null;

		return $tmdbId;

	}

	/**
	 * Get the imdbId given the tmdbId
	 */
	public static function getImdbId($tmdbId) {

		if(empty($tmdbId)) {
			return;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://api.themoviedb.org/3/movie/$tmdbId?api_key=" . urlencode(TMDB_API_KEY));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$json = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);

		if(!empty($error)) {
			Util::fatal("Cannot get imdbId: " . $error);
		}

		$data = json_decode($json);
		$imdbId = $data->imdb_id;

		return $imdbId;

	}
	
	/**
	 * Convenience method used for stripping non-title information from the title prior
	 * to looking the film up in IMDb
	 */
	public static function cleanTitle($title) {
	
		if(empty($title)) {
			return $title;
		}
	
		$title = preg_replace("/[()\[\]].*/", "", $title);
	
		return $title;
	
	}

}

?>
