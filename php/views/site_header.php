<?php

if (!isset($model)) {
    die('make an array named $model and add an mapping for "title"');
}

global $CFG;

function echo_link($uri_part, $text='') {
    echo '<a href="' . link_to_url($uri_part) . '">' . (strlen($text) > 0 ? $text : $uri_part) . '</a>';
}


$model['navigation'] = array();

class NaviItem {
    
    var $uri_part;
    var $name;
    var $children;
    var $hidden;
    
    function __construct($uri_part, $name, $hidden = FALSE) {
	$this->uri_part = $uri_part;
	$this->name = $name;
	$this->children = array();
	$this->hidden = $hidden;
    }
    
    function add($naviItem) {
	$this->children[$naviItem->uri_part] = $naviItem;
    }
    
    function isHidden() {
	if (!is_bool($this->hidden)) {	
	    if (is_callable($this->hidden)) {
		return call_user_func($this->hidden);
	    }
	    return TRUE;
	}
	return $this->hidden;
    }
    
    function render() {
	
	$hidden = $this->isHidden();
	
	if ($hidden == TRUE) {
	    $abort = TRUE;
	    foreach ($this->children as $child) {
		if (!$child->isHidden()) {
		    $abort = FALSE;
		    break;
		}
	    }
	    if ($abort) {
		return;
	    }
	}
	
	echo '<li>';
	
	if ($hidden == TRUE) {
	    echo $this->name;
	} else {
	    echo_link($this->uri_part, $this->name);
	}
	
	if (count($this->children) > 0) {
	    echo '<ul>';
	    foreach ($this->children as $uri_part => $item) {
		$item->render();
	    }
	    echo '</ul>';
	}
	
	echo '</li>';
    }
    
}

foreach ($CFG["mappings"] as $uri_part => $meta) {
    
    if (substr($uri_part, 0, 1) == '@') {
	continue;
    }
    
    $exploded = explode('/', substr($uri_part, 1), 2);

//    echo '<pre>' . var_dump($exploded) . "\n" . var_dump($uri_part) . '</pre>';
    
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
