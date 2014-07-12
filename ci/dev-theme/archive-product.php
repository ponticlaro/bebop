<?php

$products = Product::tax('product_year')->in('2014')
				   ->tax('manufacturer')->in('honda')
				   ->meta('color')->in('red')
				   ->findAll();

Ponticlaro\Bebop::View('products/archive')->render([
	'title'    => 'Products',
	'products' => $products
]);