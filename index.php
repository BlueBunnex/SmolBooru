<!DOCTYPE html>
<html>
<head>
	<title>bunsbooru</title>
	<style>
		body { font-family: sans-serif; color: #ddd; background: #212; text-align: center; }
		a { color: #ea2; text-decoration-style: dotted; }
		h1 { color: #ea2; font-size: 6em; margin-bottom: 0; }

		img { border: 2px solid transparent; height: 200px; }
	</style>

	<?php
		/*

		/
		/boobs
		/boobs/3423098

		*/
		$url_parts = explode("/", $_SERVER["REQUEST_URI"]);

		$curr_board = $url_parts[1];

		if ($url_parts[1] == "") {

			$page_type = "home";

		} else if (count($url_parts) == 2 || ($url_parts[2] == "")) {

			$page_type = "board";

		} else {

			$page_type = "image";
		}
	?>
</head>
<body>

	<h1>bunsbooru</h1>

	<nav>
		<?php
			
			$dbString = file_get_contents('db.json');
			$db = json_decode($dbString, true);

			// insert button to each board
			foreach (array_keys($db) as $board) {

				if ($board == $curr_board) {

					echo "[ <strong>$board</strong> ] ";

				} else {

					echo "[ <a href='/board/$board'>$board</a> ] ";
				}
			}
		?>
	</nav>

	<?php

		if ($page_type == "home") {

			// just show a random image
			echo "<br><br><br><br><br><br>";
			echo "<img style='height: 300px;' src='image_db/" . array_keys($db["hourglass"])[0] . ".jpg'>";
			echo "<br>A random image!";

		}

		else if ($page_type == "board") {

			echo "board";

		}
	
		else if ($page_type == "image") {

			echo "<h2>[<a href='/$url_parts[1]'>back</a>] $url_parts[2]</h2>";

			echo "<img style='height: auto; max-width: 100%; max-height: 90vh;' src='/image_db/" . $url_parts[2] . ".jpg'>";
		}

	?>

</body>
</html>
