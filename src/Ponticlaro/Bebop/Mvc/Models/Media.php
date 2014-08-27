<?php

namespace Ponticlaro\Bebop\Mvc\Models;

class Media extends Post {

	/**
     * Post type name
     * 
     * @var string
     */
    protected static $__type = 'attachment';

    /**
     * Checks is a thumbnail with the target size id exists
     * 
     * @param  string  $size           Target size id
     * @param  array   $fallback_sizes Fallback size ids
     * @return boolean                 True if exists, false otherwise
     */
    public function hasThumb($size, array $fallback_sizes = array())
    {
    	$has_thumb = false;

    	if (isset($this->sizes[$size])) {
    		
    		$has_thumb = true;
    	}

    	if (!$has_thumb && $fallback_sizes) {
    		
    		foreach ($fallback_sizes as $size) {
    			
    			if (is_string($size) && isset($this->sizes[$size])) {
    				
    				$has_thumb = true;
    				break;
    			}
    		}
    	}

    	return $has_thumb;
    }

    /**
     * Returns thubmnail data for the target thumbnail id
     * 
     * @param  string $size           Target size id
     * @param  array  $fallback_sizes Fallback size ids
     * @return object                 Thumbnail url, widht and height
     */
    public function getThumb($size, array $fallback_sizes = array())
    {
    	if (!is_string($size)) return null;
    	
        $thumb = null;

    	if ($this->hasThumb($size)) {
    		
    		$thumb = $this->sizes[$size];
    	}

    	if (!$thumb && $fallback_sizes) {
    		
    		foreach ($fallback_sizes as $size) {
    			
    			if (is_string($size) && $this->hasThumb($size)) {
    				
    				$thumb = $this->sizes[$size];
    				break;
    			}
    		}
    	}

    	return $thumb;
    }

    /**
     * Returns the thumbnail URL for the target thmbnail id
     * 
     * @param  string $size           Target size id
     * @param  array  $fallback_sizes Fallback size ids
     * @return string                 Thumbnail URL
     */
    public function getThumbUrl($size, array $fallback_sizes = array())
    {
    	$thumb = $this->getThumb($size, $fallback_sizes);

    	return $thumb ? $thumb->url : null;
    }
}