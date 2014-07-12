<?php use Ponticlaro\Bebop;

$this->partial('partials/header');

var_dump(Bebop::Context()->getCurrent());
var_dump(Bebop::Context()->is('/single\/(product|post|page)/', true));

var_dump($product);

$this->partial('products/partials/full-product', (array) $product);
$this->partial('partials/footer', ['id' => 'FOOTER']);