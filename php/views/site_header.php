<?php

if (!isset($model)) {
    die('make an array named $model and add an mapping for "title"');
}

?>

<!doctype html>
<html class="no-js" lang="en">
	<head>
		<title><?php echo $model['title']; ?></title>
	</head>
	<body>
	
	<h1><?php echo $model['title']; ?></h1>
	
	<div class="main">
	
	<?php /* view html from here */
