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
	<?php

		// hide warnings
		ini_set('display_errors','Off');
		ini_set('error_reporting', E_ALL );
		define('WP_DEBUG', false);
		define('WP_DEBUG_DISPLAY', false);
	?>
</head>
<body>

	<h1>localbooru</h1>

	<div>
		<?php

		$_BOARDS = [ "fat", "hourglass" ];
			
			$dbString = file_get_contents('db.json');
			$db = json_decode($dbString, true);

			foreach ($_BOARDS as $board) {

				$ids = array_keys($db[$board]);
				$id = $ids[0];

				echo "<a href='board/$board' style='display: inline-block; width: 200px; aspect-ratio: 1; background-image: url(\"image_db/$id.jpg\"); background-size: cover; background-position: center; color: black; font-size: 2em; text-shadow: 0 0 4px white;'>$board</a>";
			}
		?>
	</div>

</body>
</html>
