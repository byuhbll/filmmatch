<?php

// NO WARRANTY OR SUPPORT OF ANY KIND IS PROVIDED FOR THIS SOFTWARE.

require_once('../inc/include.php');

$done = isset($_GET['done']) ? true : false;
$status = isset($_GET["status"]) ? $_GET["status"] : null;


$db = new DB();
$records = $db->selectByStatus($status);

$totals = $db->totals();
$yesTotal = isset($totals["yes"]) ? $totals["yes"] : 0;
$noTotal = isset($totals["no"]) ? $totals["no"] : 0;
$toGoTotal = isset($totals[null]) ? $totals[null] : 0;

?>

<html>
<head>
<title>Film Match | Results</title>
<style type="text/css">
table { border-collapse: collapse }
td,th { border-style: solid; border-width: 1px; padding: 4px; }
</style>
</head>
<body>
	<?php if($done) { ?><h1>Thank you for your help. We're all done.</h1><?php } ?>
	<h1>Totals</h1>
	<p>To Go: <a href="results.php"><?php echo $toGoTotal;?></a></p>
	<p>Matched: <a href="results.php?status=yes"><?php echo $yesTotal;?></a></p>
	<p>No Match: <a href="results.php?status=no"><?php echo $noTotal;?></a></p>
	<h1><?php print empty($status) ? "to go" : $status ?></h1>
	<table>
		<tr>
			<th>Cat ID</th>
			<th>TMDB ID</th>
			<th>IMDb ID</th>
			<th>Title</th>
			<th>Notes</th>
			<th>Edit</th>
		</tr>
		<?php foreach($records as $record) { ?>
			<tr>
				<td><a href="<?php print str_replace("{{CAT_ID}}", $record->getCatId(), CATALOG_TEMPLATE) ?>"><?php print $record->getCatId() ?></a></td>
				<td><a href="http://www.themoviedb.org/movie/<?php print $record->getTmdbId() ?>"><?php print $record->getTmdbId() ?></a></td>
				<td><a href="http://www.imdb.com/title/<?php print $record->getImdbId() ?>"><?php print $record->getImdbId() ?></a></td>
				<td><?php print $record->getTitle()  ?></td>
				<td><?php print $record->getNotes()  ?></td>
				<td><a href="<?php print "match.php?catId=".urlencode($record->getCatId())."&tmdbId=".urlencode($record->getTmdbId())."&imdbId=".urlencode($record->getImdbId()) ?>">edit</a></td>
			</tr>
		<?php } ?>
	</table>
</body>
</html>
