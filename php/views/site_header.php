<?php

if (!isset($model)) {
    die('make an array named $model and add an mapping for "title"');
}

global $CFG;

$model['navigation'] = array();

foreach ($CFG["mappings"] as $uri_part => $meta) {
    
    if (substr($uri_part, 0, 1) == '@') {
	continue;
    }
    
    $exploded = explode('/', substr($uri_part, 1), 2);

    $item = new NaviItem($uri_part, 
			 (is_array($meta) && array_key_exists('name', $meta) 
			  ?  $meta["name"] 
			  : (count($exploded) == 2 ? $exploded[1] : $exploded[0])),
			 (is_array($meta) && array_key_exists('hidden', $meta)
			  ? $meta["hidden"]
			  : FALSE));
    
    if (array_key_exists($exploded[0], $model["navigation"])) {
	$model["navigation"][$exploded[0]]->add($item);
	continue;
    }
    
    $model["navigation"][$exploded[0]] = $item;
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
<?php 
foreach ($model["navigation"] as $item) { $item->render(); }
?>
		</ul>
	</div>

	<div class="main">
	
	<?php /* view html from here */
