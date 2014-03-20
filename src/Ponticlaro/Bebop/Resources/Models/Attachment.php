<?php

namespace Ponticlaro\Bebop\Resources\Models;

class Attachment {

	public function __construct(\WP_Post $post)
	{
		///////////////////////////////////
		// Inherit all $post properties  //
		///////////////////////////////////
		foreach ((array) $post as $key => $value) {
			$this->{$key} = $value;
		}

		// Get permalink
		$this->permalink = get_permalink($post->ID);

		$this->sizes = get_intermediate_image_sizes();

		///////////
		// Media //
		///////////
		$this->sizes = new \stdClass;
		
		foreach (get_intermediate_image_sizes() as $size) {
			
			$image_data = wp_get_attachment_image_src($this->ID, $size);

			if ($image_data) {
				$this->sizes->$size = array(
					'url'     => $image_data[0],
					'width'   => $image_data[1],
					'height'  => $image_data[2],
					'resized' => $image_data[3],
				);
			}
		}
	}

	public static function getById($id)
	{
		if (!$id || intval($id) == 0) return null;
		
		$post = get_post($id);

		return $post instanceof \WP_Post ? new Attachment($post) : null;
	}
}