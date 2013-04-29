<?php

// NO WARRANTY OR SUPPORT OF ANY KIND IS PROVIDED FOR THIS SOFTWARE.

require_once('../inc/include.php');

$catId = isset($_GET["catId"]) ? $_GET["catId"] : null;
$tmdbId = isset($_GET["tmdbId"]) ? $_GET["tmdbId"] : null;
$imdbId = isset($_GET["imdbId"]) ? $_GET["imdbId"] : null;

$db = new DB();

$record = new Record($catId, $tmdbId, $imdbId, null, null, 0);

// reset the record status, it will normally be already reset at this
// point unless user came from list screen by clicking on edit
// or user manually entered in url
$db->resetRecordStatus($catId);

$record = Lookup::populate($record);

$catId = $record->getCatId();
$tmdbId = $record->getTmdbId();
$imdbId = $record->getImdbId();

$leftFrame = str_replace("{{CAT_ID}}", $catId, CATALOG_TEMPLATE);
$rightFrame = "http://www.imdb.com/title/$imdbId";

$totals = $db->totals();
$yesTotal = isset($totals["yes"]) ? $totals["yes"] : 0;
$noTotal = isset($totals["no"]) ? $totals["no"] : 0;
$toGoTotal = isset($totals[null]) ? $totals[null] : 0;

?>

<html>
<head>
<title>Film Match</title>
</head>
<body>
	<div style="position: absolute; top: 20px; bottom: 20px; left: 20px; right: 20px;">
		<form action="next.php" method="post">
			<div style="float: left; width: 40%; width: -webkit-calc(50% - 75px); width: calc(50% - 75px);">
				<iframe id="catalog" src="<?php echo $leftFrame; ?>" style="width: 100%; height: 100%"></iframe>
			</div>
			<div style="float: left; width: 120px; padding: 15px">
				<input type="hidden" name="catId" value="<?php echo $catId; ?>" />
				<input type="hidden" name="imdbId" value="<?php echo $imdbId; ?>" />
				<input type="hidden" name="tmdbId" value="<?php echo $tmdbId; ?>" />
				<input type="hidden" name="index" value="<?php echo $index; ?>" />
				<p style="text-align: center; font-size: 24">Are these films the same?</p>
				<p style="text-align: center; margin-top: 4px; margin-bottom: 4px"><input type="submit" name="status" value="Yes" style="font-size: 32" /></p>
				<p style="text-align: center; margin-top: 4px; margin-bottom: 4px"><input type="submit" name="status" value="No" style="font-size: 32" /></p>
				<p style="text-align: center; margin-top: 4px; margin-bottom: 4px"><input type="submit" name="status" value="Never" style="font-size: 32" /></p>
				<p style="text-align: center; margin-top: 4px; margin-bottom: 4px"><input type="submit" name="status" value="Skip" style="font-size: 32" /></p>
				<p style="text-align: center; margin-top: 32px">Change IMDb<br /><input type="text" name="newImdbId" value="<?php echo $imdbId ?>" style="width: 100%; " /><br /><input type="submit" name="status" value="Change" style="font-size: 16" /></p>
				<div style="margin-top: 32px; text-align: center">
					<p>To Go: <a href="results.php"><?php echo $toGoTotal;?></a><br />Matched: <a href="results.php?status=yes"><?php echo $yesTotal;?></a><br />No Match: <a href="results.php?status=no"><?php echo $noTotal;?></a></p>
				</div>
				<p style="margin-top: 32px; text-align: center"><a href="help.html">Help</a></p>
			</div>
			<div style="float: left; width: 40%; width: -webkit-calc(50% - 75px); width: calc(50% - 75px);">
				<iframe id="imdb" src="<?php echo $rightFrame; ?>" style="width: 100%; height: 100%"></iframe>
			</div>
		</form>
	</div>
</body>
</html>
