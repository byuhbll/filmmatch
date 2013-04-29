<?php

// NO WARRANTY OR SUPPORT OF ANY KIND IS PROVIDED FOR THIS SOFTWARE.

require_once('../inc/include.php');

$catId = isset($_POST['catId']) ? $_POST['catId'] : null;
$tmdbId = isset($_POST['tmdbId']) ? $_POST['tmdbId'] : null;
$imdbId = isset($_POST['imdbId']) ? $_POST['imdbId'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : null;
$newImdbId = isset($_POST['newImdbId']) ? $_POST['newImdbId'] : null;

$db = new DB();
$db->insertHistory($catId, $tmdbId, $imdbId, $status);

if(!empty($catId)) {
	if($status == "Yes") {
		$db->insertResult($catId, $tmdbId, $imdbId, "yes");
	} else if($status == "No") {
		$db->insertResult($catId, $tmdbId, $imdbId, "no");
	} else if($status == "Never") {
		$db->setRecordStatus($catId, "no");
	} else if($status == "Change") {
		$record = new Record($catId, "", $newImdbId);
		$next = Lookup::populate($record);
	}
}

if(empty($next)) {
	$next = Lookup::next();
}

if(empty($next)) {
	header("Location: results.php?done=1&status=yes");
} else {
	$uri = "match.php?catId=".urlencode($next->getCatId())."&tmdbId=".urlencode($next->getTmdbId())."&imdbId=".urlencode($next->getImdbId());
	header("Location: $uri");
}

exit;

?>
