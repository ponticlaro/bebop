<?php

Db::query('posts')->type('product');

$data = Db::rawQuery($args)->execute();


$pdo = Bebop::Db()->getConnection();

$query = Bebop::Db()->query('posts');
$query->type('product')
	  ->meta('product_year')->between(2004, 2014)
	  ->ppp(16)
	  ->findAll();