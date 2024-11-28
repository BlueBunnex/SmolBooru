<!DOCTYPE html>
<html>
<head>
	<title>localbooru</title>
	<style>
		body { font-family: sans-serif; color: #ddd; background: #212; text-align: center; }
		a { color: #ea2; }
		h1 { color: #ea2; font-size: 6em; }

		img { border: 2px solid transparent; height: 200px; }
	</style>
</head>
<body>

	<h1>localbooru</h1>

	<div>
		<?php
			
			$dbString = file_get_contents('db.json');
			$db = json_decode($dbString, true);

			// insert button to each board
			foreach ($db["boards"] as $board) {

				if (array_key_exists($board, $db) && count($db[$board]) > 0) {

					// if board entry already exists in json, pog
					$ids = array_keys($db[$board]);
					$id = $ids[0];

					echo "<a href='board/$board' style='display: inline-block; width: 200px; aspect-ratio: 1; background-image: url(\"image_db/$id.jpg\"); background-size: cover; background-position: center; color: black; font-size: 2em; text-shadow: 0 0 4px white;'>$board</a>";

				} else {

					// if board entry doesn't exist in json, or it's empty, the button can't have an image background
					echo "<a href='board/$board' style='display: inline-block; width: 200px; aspect-ratio: 1; background: pink; color: black; font-size: 2em; text-shadow: 0 0 4px white;'>$board</a>";
				}
			}
		?>
	</div>

</body>
</html>
