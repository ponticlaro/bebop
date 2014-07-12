<?php use Ponticlaro\Bebop;

$this->partial('partials/header', ['title' => $title]);

if ($products) { ?>
	
	<ul>
		<?php foreach ($products as $product) {
			
			$this->partial('products/partials/archive-item', (array) $product);

		} ?>
	</ul>

<?php }

else {

	$this->partial('products/partials/no-results');
}

$this->partial('partials/footer');