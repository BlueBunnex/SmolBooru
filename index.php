<!DOCTYPE html>
<html>
<head>
	<title>bunsbooru</title>
	<style>
		body { font-family: sans-serif; color: #ddd; background: #212; margin: 0; }
		a { color: #ea2; }
		h1 { font-size: 3em; margin: 1rem; }

		img { border: 2px solid transparent; height: 200px; }
	</style>

	<?php

		// hide warnings
		ini_set('display_errors','Off');
		ini_set('error_reporting', E_ALL );
		define('WP_DEBUG', false);
		define('WP_DEBUG_DISPLAY', false);



		$url_parts = explode("/", $_SERVER["REQUEST_URI"]);

		$curr_board = $url_parts[1];

		if ($url_parts[1] == "") {

			$page_type = "home";

		} else if (count($url_parts) == 2 || ($url_parts[2] == "")) {

			$page_type = "board";

		} else {

			$page_type = "image";
		}

		// make sure the selected board exists
		if ($page_type != "home") {

			$dbString = file_get_contents('db.json');
			$db = json_decode($dbString, true);

			if (!in_array($url_parts[1], array_keys($db))) {

				echo "Invalid board! Are you trying to be sneaky?";
				return;
			}
		}
	?>

	<?php // this PHP block has ALL of the post code. it's kinda messy

	// if /image_db doesn't exist, uh, it should lol

	if ($page_type == "board") {

		$response = "";
		$response_color = "black";

		if ($_SERVER["REQUEST_METHOD"] == "POST") {

			$response = postImage($board);

			if ($response == null) {

				$response = "Successfully uploaded!";
				$response_color = "green";

			} else {

				$response_color = "red";
			}
		}

		// php -S localhost:8000
		// https://www.w3schools.com/php/php_forms.asp

		function postImage($board) {

			$file_url = htmlspecialchars($_POST["file"]);

			if ($file_url != "") {

				$file_content   = file_get_contents($file_url);
				$file_extension = pathinfo(explode("?", $file_url)[0])["extension"];

				$image_id = getIdWithoutCollision($board);

				if ($file_content) {

					if (
						file_put_contents("image_db/" . $image_id . "." . $file_extension, $file_content)
						&& normalizeLocalImage($image_id, $file_extension)
						&& addLocalImageToDB($image_id, $board)
					) {

						return null;

					} else {

						return "Could not download!";
					}

				} else {

					return "Bad URL!";
				}

			} else {

				return "URL cannot be blank!";
			}
		}

		// generate an image ID while checking for collisions
		function getIdWithoutCollision($board) {

			$image_id = rand(0, 10000000);

			$dbString = file_get_contents('db.json');
			$db = json_decode($dbString, true);

			if ($db[$board] == null) {
				
				return $image_id;
			}

			while ($db[$board][$image_id] != null) { // should probably limit how many times it can loop, but uh, w/e

				$image_id = rand(0, 10000000);
			}

			return $image_id;
		}

		// normalizes to jpeg with height=800px
		function normalizeLocalImage($image_id, $image_extension) {

			switch ($image_extension) {

				case "jpg":
				case "jpeg":
					$file_content = imagecreatefromjpeg("image_db/" . $image_id . "." . $image_extension);
					break;

				case "png":
					$file_content = imagecreatefrompng("image_db/" . $image_id . "." . $image_extension);
					break;

				case "webp":
					$file_content = imagecreatefromwebp("image_db/" . $image_id . "." . $image_extension);
					break;

				default:
					return false;
			}

			if (imagesy($file_content) > 800)
				$file_content = imagescale($file_content, (int) (imagesx($file_content) * 800 / imagesy($file_content)), 800);

			if (!imagejpeg($file_content, "image_db/" . $image_id . ".jpg", 100)) {

				return false;
			}

			if ($image_extension != "jpg")
				unlink("image_db/" . $image_id . "." . $image_extension);

			return true;
		}

		function addLocalImageToDB($image_id, $board) {

			$dbString = file_get_contents('db.json');
			$db = json_decode($dbString, true);

			if ($db[$board] == null) {

				$db[$board] = array();
			}

			$db[$board][$image_id] = array();

			$dbString = json_encode($db);
			file_put_contents('db.json', $dbString);

			return true;
		}
	}
	?>
</head>
<body>

	<h1><a href="/">bunsbooru</a></h1>

	<nav style="padding: 1em; background: black;">
		<?php
			
			$dbString = file_get_contents('db.json');
			$db = json_decode($dbString, true);

			// insert button to each board
			foreach (array_keys($db) as $board) {

				if ($page_type == "board" && $board == $curr_board) {

					echo "[ <strong>$board</strong> ] ";

				} else {

					echo "[ <a href='/$board'>$board</a> ] ";
				}
			}
		?>
	</nav>

	<div style="padding: 1em;">

		<?php

			if ($page_type == "home") {

				// just show a random image
				echo "<img style='height: 300px;' src='image_db/" . array_keys($db["hourglass"])[0] . ".jpg'>";
				echo "<br>A random image!";

			}

			else if ($page_type == "board") {

				echo <<<EOL
				<fieldset style="width: 30em; border-color: #545;">
					<legend>Post image to <strong>$url_parts[1]</strong></legend>
					<form method="post">
						<input type="text" name="file" placeholder="File URL... (I'll replace this with file upload later but it's hard sob)" style="width: 100%;">

						<br><br>

						<input type="submit" value="Post">
						<span style="color: $response_color;">$response</span>
					</form>
				</fieldset>

				<br><br><br>
				EOL;

				// add image to json
				$dbString = file_get_contents('db.json');
				$db = json_decode($dbString, true);

				echo "<h2><span style='font-weight: 400;'>Images in</span> $url_parts[1]</h2>";

				if ($db[$url_parts[1]] != null) {

					foreach (array_keys($db[$url_parts[1]]) as $id) {

						echo "<a href='/$url_parts[1]/$id'><img src='/image_db/$id.jpg'></a>";
					}
				}

			}
		
			else if ($page_type == "image") {

				echo "<h2>$url_parts[2] ($url_parts[1])</h2>";

				echo "<img style='height: auto; max-width: 100%; max-height: 90vh;' src='/image_db/" . $url_parts[2] . ".jpg'>";
			}

		?>

	</div>

</body>
</html>
