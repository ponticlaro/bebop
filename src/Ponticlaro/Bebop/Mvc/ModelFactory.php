<?php

namespace Ponticlaro\Bebop\Mvc;

use Ponticlaro\Bebop\Patterns\FactoryAbstract;

class ModelFactory extends FactoryAbstract {

	/**
	 * List of manufacturable classes
	 * 
	 * @var array
	 */
	protected static $manufacturable = array(
		'post'       => 'Ponticlaro\Bebop\Mvc\Models\Post',
		'attachment' => 'Ponticlaro\Bebop\Mvc\Models\Media'
	);
}