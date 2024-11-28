<!DOCTYPE html>
<html>
<head>
	<title>localbooru</title>
	<style>
		body { font-family: sans-serif; }
		img { height: 200px; border: 1px solid lightgrey; }
	</style>
	<?php

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

			$file_url = htmlspecialchars($_POST["file"]);

			if ($file_url != "") {

				$file_content = file_get_contents($file_url);
				$file_extension = pathinfo(explode("?", $file_url)[0])["extension"];

				// generate an image ID while checking for collisions
				$image_id = rand(0, 10000000);

				if ($file_content) {

					if (
						file_put_contents("img/" . $image_id . "." . $file_extension, $file_content)
						&& normalizeLocalImage($image_id, $file_extension)
						&& addLocalImageToDB($image_id)
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

			if (!imagejpeg($file_content, "img/" . $image_id . ".jpg")) {

				return false;
			}

			if ($image_extension != "jpg")
				unlink("img/" . $image_id . "." . $image_extension);

			return true;
		}

		function addLocalImageToDB($image_id) {

			$dbString = file_get_contents('db.json');
			$db = json_decode($dbString, true);

			$db[$image_id] = array();

			$dbString = json_encode($db);
			file_put_contents('db.json', $dbString);

			return true;
		}
	?>
</head>
<body>
	
	<fieldset style="width: 50em; margin: auto;">
		<legend>Add to booru</legend>
		<form method="post">
			<input type="text" name="file" placeholder="File URL..." style="width: 100%;">
			<br><br>
			radio buttons for which board to put it under
			<br><br>
			<input type="submit" value="Post">
			<span style="color: <?php echo $response_color; ?>;"><?php echo $response; ?></span>
		</form>
	</fieldset>

	<h1>localbooru</h1>

	<div>
		<?php
			
			// add image to json
			$dbString = file_get_contents('db.json');
			$db = json_decode($dbString, true);

			foreach (array_keys($db) as $id) {
				echo "<img src='img/" . $id . ".jpg'>";
			}
		?>
	</div>

</body>
</html>
