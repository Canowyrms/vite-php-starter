<?php
/**
 * Webapp entrypoint.
 */

declare(strict_types=1);

/** @var string Absolute path to the project root. No trailing slash. */
$ROOT = dirname(__DIR__);

require $ROOT . '/vendor/autoload.php';

use App\Utils\Vite;

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<title>Vite PHP Starter</title>

	<?= Vite::clientScriptHTML(); ?>
	<?= Vite::assetHTML('main.js'); ?>
</head>
<body>
	<div class="example"></div>
	<!-- <pre><?= $ROOT; ?></pre>
	<hr>
	<pre><?= Vite::$VITE_HOST; ?></pre>
	<hr>
	<pre>
		<?php print_r(getenv()); ?>
	</pre> -->
</body>
</html>
