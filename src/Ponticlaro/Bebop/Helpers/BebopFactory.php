<?php

namespace Ponticlaro\Bebop\Helpers;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Patterns\FactoryAbstract;
use Ponticlaro\Bebop\Patterns\TrackableObjectAbstract;

class BebopFactory extends FactoryAbstract {

	/**
	 * List of manufacturable classes
	 * 
	 * @var array
	 */
	protected static $manufacturable = array(
		'AdminPage'  => '\Ponticlaro\Bebop\AdminPage',
		'Collection' => '\Ponticlaro\Bebop\Common\Collection',
		'Metabox'    => '\Ponticlaro\Bebop\Metabox',
		'Option'     => '\Ponticlaro\Bebop\Db\Option',
		'PostType'   => '\Ponticlaro\Bebop\PostType',
		'Taxonomy'   => '\Ponticlaro\Bebop\Taxonomy'
	);

	/**
	 * Creates instance of target class
	 * 
	 * @param  string $type Class ID
	 * @param  array  $args Class arguments
	 * @return object       Class instance
	 */
	public static function create($type, array $args = array())
	{
		// Create object
		$obj = parent::create($type, $args);

		// Track object if trackable
	    if ($obj instanceof TrackableObjectAbstract) Bebop::track($obj);

	    return $obj;
	}
}