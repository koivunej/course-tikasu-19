<?php

/**
 * site_templating.php
 * 
 * functions for rendering the common site template.
 * each view/controller must define a $model array containing
 * at minimum mapping for key 'title'.
 * 
 * "rendering" also requires the global $CFG variable.
 */

// renders the top half of the site page

function render_template_begin($model) {
    __render_template($model, 'site_header.php');
}

// renders the bottom half of the site page

function render_template_end($model) {
    __render_template($model, 'site_footer.php');
}

// internal rendering helper

function __render_template($model, $name) {
    if (!isset($model) || $model == NULL) {
	die('null $model');
    }
    global $CFG;
    include $CFG['dirs']['views'] . '/' . $name;
}
