<?php

// NO WARRANTY OR SUPPORT OF ANY KIND IS PROVIDED FOR THIS SOFTWARE.

require_once('../inc/include.php');

class DB {

	protected $con;

	/**
	 *
	 */
	public function __construct() {

		$this->con = mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD) or Util::fatal(mysql_error());
		mysql_select_db(DB_NAME, $this->con) or Util::fatal(mysql_error());

	}

	/**
	 *
	 */
	public function __destruct() {

		if(!empty($con)) {
			mysql_close($this->con);
		}

	}
	
	/**
	 * Get the next record from Record that needs human verification
	 */
	public function selectNextRecord() {
	
		$query = "SELECT * FROM Record WHERE status IS NULL ORDER BY queue LIMIT 1";
		$result = mysql_query($query, $this->con) or Util::fatal(mysql_error());
	
		while($row = mysql_fetch_array($result)) {
			$record = $this->createRecord($row);
			$this->sendRecordToBack($record->getCatId());
			return $record;
		}
	
		return null;
	
	}
	
	/**
	 * Insert a human verified film in to the Result table
	 */
	public function insertResult($catId, $tmdbId, $imdbId, $status) {

		if(empty($catId)) {
			return;
		}

		$catIdEscaped = mysql_real_escape_string($catId);
		$tmdbIdEscaped = mysql_real_escape_string($tmdbId);
		$imdbIdEscaped = mysql_real_escape_string($imdbId);
		$statusEscaped = mysql_real_escape_string($status);
		
		$query = "INSERT INTO Result (catId, tmdbId, imdbId, status) VALUES ('$catIdEscaped', '$tmdbIdEscaped', '$imdbIdEscaped', '$statusEscaped') ON DUPLICATE KEY UPDATE status = '$statusEscaped', modified = NOW()";
		$result = mysql_query($query, $this->con) or Util::fatal(mysql_error());
		
		if($status == 'yes') {
			$this->setRecordStatus($catId, $status);
		}

	}
	
	/**
	 * Insert a button click into the History
	 */
	public function insertHistory($catId, $tmdbId, $imdbId, $status) {
	
		if(empty($catId)) {
			return;
		}
	
		$catIdEscaped = mysql_real_escape_string($catId);
		$tmdbIdEscaped = mysql_real_escape_string($tmdbId);
		$imdbIdEscaped = mysql_real_escape_string($imdbId);
		$statusEscaped = mysql_real_escape_string($status);
	
		$query = "INSERT History (catId, tmdbId, imdbId, status) VALUES ('$catIdEscaped', '$tmdbIdEscaped', '$imdbIdEscaped', '$statusEscaped')";
		$result = mysql_query($query, $this->con) or Util::fatal(mysql_error());
	
	}

	/**
	 * Set the status of the record in the Record table
	 */
	public function setRecordStatus($catId, $status) {

		if(empty($catId)) {
			return;
		}

		$catIdEscaped = mysql_real_escape_string($catId);
		$statusEscaped = mysql_real_escape_string($status);

		$query = "UPDATE Record SET status = '$statusEscaped' WHERE catId = '$catIdEscaped'";
		$result = mysql_query($query, $this->con) or Util::fatal(mysql_error());

	}
	
	/**
	 * Set status to null in the Record table
	 */
	public function resetRecordStatus($catId) {
	
		if(empty($catId)) {
			return;
		}
	
		$catIdEscaped = mysql_real_escape_string($catId);
	
		$query = "UPDATE Record SET status = NULL WHERE catId = '$catIdEscaped'";
		$result = mysql_query($query, $this->con) or Util::fatal(mysql_error());
	
	}

	/**
	 * Select all records with a given status for the results page
	 */
	public function selectByStatus($status) {

		$statusEscaped = mysql_real_escape_string($status);

		$query = "SELECT r2.*, r1.* FROM Record r1 LEFT JOIN Result r2 ON r1.catId = r2.catId AND r1.status = r2.status WHERE r1.status " . (empty($statusEscaped) ? "IS NULL" : "= '$statusEscaped'");
		$result = mysql_query($query, $this->con) or Util::fatal(mysql_error());

		$records = array();

		while($row = mysql_fetch_array($result)) {
			$record = $this->createRecord($row);
			array_push($records, $record);
		}

		return $records;

	}


	/**
	 * When a film is selected for human verification, it is sent to the end
	 * of the queue so the next person doesn't get the same film
	 */
	public function sendRecordToBack($catId) {

		if(empty($catId)) {
			return;
		}

		$catIdEscaped = mysql_real_escape_string($catId);

		$query = "SELECT MAX(queue) as queue From Record";
		$result = mysql_query($query, $this->con) or Util::fatal(mysql_error());

		$maxQueue = 0;

		while($row = mysql_fetch_array($result)) {
			$maxQueue = $row['queue'];
		}
		
		$this->setQueue($catId, $maxQueue + 1);

	}

	/**
	 * Sets the film to a particular position in the queue
	 */
	public function setQueue($catId, $queue) {

		if(empty($catId)) {
			return;
		}

		$catIdEscaped = mysql_real_escape_string($catId);
		$queueEscaped = mysql_real_escape_string($queue);

		// update number of queue so as to pull the least viewed records
		$query = "UPDATE Record SET queue = $queueEscaped WHERE catId = '$catIdEscaped'";
		$result = mysql_query($query, $this->con) or Util::fatal(mysql_error());

	}
	
	/**
	 * Selects all tmdbIds that have already been human verified given a catId
	 * This includes positive and negative matches
	 */
	public function selectCheckedTmdbIds($catId) {
		
		if(empty($catId)) {
			return;
		}
		
		$catIdEscaped = mysql_real_escape_string($catId);
		
		// update number of queue so as to pull the least viewed records
		$query = "SELECT tmdbId FROM Result WHERE catId = '$catIdEscaped'";
		$result = mysql_query($query, $this->con) or Util::fatal(mysql_error());
		
		$tmdbIds = array();
		
		while($row = mysql_fetch_array($result)) {
			$tmdbId = $row["tmdbId"];
			array_push($tmdbIds, $tmdbId);
		}
		
		return $tmdbIds;
		
	}
	
	/**
	 * Returns films totals for each status
	 */
	public function totals() {

		$query = "SELECT status, COUNT(*) as total FROM Record GROUP BY status";
		$result = mysql_query($query, $this->con) or Util::fatal(mysql_error());

		$totals = array();

		while($row = mysql_fetch_array($result)) {
			$totals[$row["status"]] = $row["total"];
		}

		return $totals;

	}

	/**
	 * Convenience method for creating a Record object given a row from the database
	 */
	private function createRecord($row) {

		if(empty($row)) {
			return null;
		}

		$record = new Record($row["catId"], $row["tmdbId"], $row["imdbId"], $row["title"], null, $row["queue"], $row["notes"]);

		return $record;
	}

}

?>
