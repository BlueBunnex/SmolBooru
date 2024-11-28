<!DOCTYPE html>
<html>
<head>
	<title>localbooru</title>
	<style>
		body { font-family: sans-serif; color: #ddd; background: #212; text-align: center; }
		a { color: #ea2; text-decoration: none; }
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

		foreach ($url_parts as $part) {

			echo $part . "<br>";
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
			foreach ($db["boards"] as $board) {

				echo "[ <a href='board/$board'>$board</a> ] ";
			}
		?>
	</nav>

</body>
</html>
