<?php

Ponticlaro\Bebop::View('products/single')->render([
	'product' => Product::find()
]);