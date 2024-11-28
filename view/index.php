<!DOCTYPE html>
<html>
<head>
	<title>localbooru</title>
	<style>
		body { font-family: sans-serif; color: #ddd; background: #212; text-align: center; }
		a { color: #ea2; }
		h1 { color: #ea2; font-size: 6em; }

		img { border: 2px solid transparent; max-width: 100%; max-height: 90vh; }
	</style>
	<?php

		// hide warnings
		// ini_set('display_errors','Off');
		// ini_set('error_reporting', E_ALL );
		// define('WP_DEBUG', false);
		// define('WP_DEBUG_DISPLAY', false);

		$image_id = explode("/", $_SERVER["REQUEST_URI"])[2];
	?>
</head>
<body>

	<h1>localbooru</h1>

	<h2>[<a href="/">back</a>] <?php echo $image_id; ?></h2>

	<img src="<?php echo "/img/" . $image_id . ".jpg"; ?>">

</body>
</html>
