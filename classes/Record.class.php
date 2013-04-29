<?php

// NO WARRANTY OR SUPPORT OF ANY KIND IS PROVIDED FOR THIS SOFTWARE.

require_once('../inc/include.php');

class Record {

	protected $catId;
	protected $tmdbId;
	protected $imdbId;
	protected $title;
	protected $notes;
	protected $status;
	protected $queue;
	
	/**
	 *
	 */
	public function __construct($catId, $tmdbId = null, $imdbId = null, $title = null, $status = null, $queue = 0, $notes = null) {
		$this->catId = $catId;
		$this->tmdbId = $tmdbId;
		$this->imdbId = $imdbId;
		$this->title = $title;
		$this->status = $status;
		$this->queue = $queue;
		$this->notes = $notes;
	}

	public function getCatId() { return $this->catId; }
	public function getTmdbId() { return $this->tmdbId; }
	public function getImdbId() { return $this->imdbId; }
	public function getTitle() { return $this->title; }
	public function getNotes() { return $this->notes; }
	public function getStatus() { return $this->status; }
	public function getQueue() { return $this->queue; }
	
	public function setCatId($catId) { $this->catId = $catId; }
	public function setTmdbId($tmdbId) { $this->tmdbId = $tmdbId; }
	public function setImdbId($imdbId) { $this->imdbId = $imdbId; }
	public function setTitle($title) { $this->title = $title; }
	public function setNotes($notes) { $this->notes = $notes; }
	public function setStatus($status) { $this->status = $status; }
	public function setQueue($queue) { $this->queue = $queue; }

}

?>

