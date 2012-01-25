<?php

if (!isset($model)) {
    die('make an array named $model and add an mapping for "title"');
}

global $CFG;

function echo_link($uri_part, $text='') {
    echo '<a href="' . link_to_url($uri_part) . '">' . (strlen($text) > 0 ? $text : $uri_part) . '</a>';
}

?>

<!doctype html>
<html class="no-js" lang="en">
	<head>
		<title><?php echo $model['title']; ?></title>
	</head>
	<body>
	
	<h1><?php echo $model['title']; ?></h1>
	
	<div class="navi">
		<ul>
		<?php foreach ($CFG['mappings'] as $uri_part => $handler) { ?>
			<li><?php echo_link($uri_part); ?></li>
		<?php } ?>
		</ul>
	</div>

	<div class="main">
	
	<?php /* view html from here */
