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

		$_BOARDS = [ "fat", "hourglass" ];

		// hide warnings
		ini_set('display_errors','Off');
		ini_set('error_reporting', E_ALL );
		define('WP_DEBUG', false);
		define('WP_DEBUG_DISPLAY', false);

		$response = "";
		$response_color = "black";

		if ($_SERVER["REQUEST_METHOD"] == "POST") {

			$response = postImage();

			if ($response == null) {

				$response = "Successfully uploaded!";
				$response_color = "green";

			} else {

				$response_color = "red";
			}
		}

		// php -S localhost:8000
		// https://www.w3schools.com/php/php_forms.asp

		function postImage() {

			global $_BOARDS;

			// if /img doesn't exist, uh, it should lol

			$board = $_POST["board"];

			if (!in_array($board, $_BOARDS)) {

				return "Invalid board! Are you trying to be sneaky?";
			}

			$file_url = htmlspecialchars($_POST["file"]);

			if ($file_url != "") {

				$file_content   = file_get_contents($file_url);
				$file_extension = pathinfo(explode("?", $file_url)[0])["extension"];

				$image_id = getIdWithoutCollision();

				if ($file_content) {

					if (
						file_put_contents("img/" . $image_id . "." . $file_extension, $file_content)
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
		function getIdWithoutCollision() {

			$image_id = rand(0, 10000000);

			$dbString = file_get_contents('db.json');
			$db = json_decode($dbString, true);

			while ($db[$image_id] != null) { // should probably limit how many times it can loop, but uh, w/e

				$image_id = rand(0, 10000000);
			}

			return $image_id;
		}

		// normalizes to jpeg with height=800px
		function normalizeLocalImage($image_id, $image_extension) {

			switch ($image_extension) {

				case "jpg":
				case "jpeg":
					$file_content = imagecreatefromjpeg("img/" . $image_id . "." . $image_extension);
					break;

				case "png":
					$file_content = imagecreatefrompng("img/" . $image_id . "." . $image_extension);
					break;

				case "webp":
					$file_content = imagecreatefromwebp("img/" . $image_id . "." . $image_extension);
					break;

				default:
					return false;
			}

			if (imagesy($file_content) > 800)
				$file_content = imagescale($file_content, (int) (imagesx($file_content) * 800 / imagesy($file_content)), 800);

			if (!imagejpeg($file_content, "img/" . $image_id . ".jpg", 100)) {

				return false;
			}

			if ($image_extension != "jpg")
				unlink("img/" . $image_id . "." . $image_extension);

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
	?>
</head>
<body>

	<h1>localbooru</h1>

	<fieldset style="width: 30em; margin: auto; text-align: left; border-color: #545;">
		<legend>Add to booru</legend>
		<form method="post">
			<input type="text" name="file" placeholder="File URL... (I'll replace this with file upload later but it's hard sob)" style="width: 100%;">

			<br><br>

			<strong>Board</strong><br>
			<?php

				foreach ($_BOARDS as $board) {
					
					echo "<input type='radio' id='$board' name='board' value='$board' checked><label for='$board'>$board</label><br>";
				}
			?>

			<br>

			<input type="submit" value="Post">
			<span style="color: <?php echo $response_color; ?>;"><?php echo $response; ?></span>
		</form>
	</fieldset>

	<br><br><br>

	<div>
		<?php
			
			// add image to json
			$dbString = file_get_contents('db.json');
			$db = json_decode($dbString, true);

			foreach ($_BOARDS as $board) {

				echo "<h2>$board</h2>";

				if ($db[$board] != null) {

					foreach (array_keys($db[$board]) as $id) {

						echo "<a href='view/$id'><img src='img/$id.jpg'></a>";
					}
				}
			}
		?>
	</div>

</body>
</html>
