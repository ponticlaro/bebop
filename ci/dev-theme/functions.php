<?php

use \Ponticlaro\Bebop;

///////////////////////////
// Autoload dependencies //
///////////////////////////
require_once dirname(__FILE__) . '/../../vendor/autoload.php';

////////////////
// Boot Bebop //
////////////////
Bebop::boot();

////////////////////////
// Custom Admin Pages //
////////////////////////

// Custom Admin Page
Bebop::AdminPage('Custom Admin Page', function() {});

// Custom Admin Sub-Page
Bebop::AdminPage('Custom Admin Sub-page', function() {}, array(
	'parent' => 'settings'
));

////////////////
// Post Types //
////////////////

// Product Post Type
$products_post_type = Bebop::PostType('Product');

////////////////
// Taxonomies //
////////////////

// Products: Manufacturer
Bebop::Taxonomy('Manufacturer', $products_post_type);

///////////////
// Metaboxes //
///////////////

// Products: Manufacturer
Bebop::Metabox('Manufacturer', $products_post_type, array(), function($data) {});