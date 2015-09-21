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
     * Contains ids of the existing image sizes
     * 
     * @var array
     */
    protected static $__sizes = [];

    /**
     * Contains data for each existing image size
     * @var array
     */
    public $sizes = [];

    /**
     * Checks is a thumbnail with the target size id exists
     * 
     * @param  string  $size           Target size id
     * @param  array   $fallback_sizes Fallback size ids
     * @return boolean                 True if exists, false otherwise
     */
    public function hasThumb($size, array $fallback_sizes = array())
    {
        static::__cacheExistingImageSizes();

        $has_thumb = false;

        if (in_array($size, static::$__sizes)) {
            
            $has_thumb = true;
        }

        if (!$has_thumb && $fallback_sizes) {
            
            foreach ($fallback_sizes as $size) {
                
                if (is_string($size) && in_array($size, static::$__sizes)) {
                    
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
        
        static::__cacheExistingImageSizes();

        $thumb = null;

        if ($this->hasThumb($size)) {
            
            $thumb = $this->getThumbInfo($size);
        }

        if (!$thumb && $fallback_sizes) {
            
            foreach ($fallback_sizes as $size) {
                
                if (is_string($size) && $this->hasThumb($size)) {

                    $thumb = $this->getThumbInfo($size);
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
        static::__cacheExistingImageSizes();

        $thumb = $this->getThumb($size, $fallback_sizes);

        return $thumb ? $thumb->url : null;
    }

    /**
     * Returns info for the target thumbnail id
     * 
     * @param  string $size [description]
     * @return array        Url, height, width and if the image is cropped
     */
    public function getThumbInfo($size)
    {
        static::__cacheExistingImageSizes();

        if (!isset($this->sizes[$size])) {
            
            $image_data = wp_get_attachment_image_src($this->ID, $size);

            if ($image_data) {

                $this->sizes[$size] = (object) array(
                    'url'     => $image_data[0],
                    'width'   => $image_data[1],
                    'height'  => $image_data[2],
                    'resized' => $image_data[3],
                );
            }
        }

        return isset($this->sizes[$size]) ? $this->sizes[$size] : null;
    }

    /**
     * Used to cache data for all existing image sizes 
     * 
     * @return void
     */
    public function cacheAllImageSizes()
    {
        static::__cacheExistingImageSizes();

        foreach (static::$__sizes as $size) {
            $this->getThumbInfo($size);
        }
    }

    /**
     * Caches ids of all the existing image sizes registered with WordPress
     * 
     * @return void
     */
    protected static function __cacheExistingImageSizes()
    {
        if (!static::$__sizes)
            static::$__sizes = get_intermediate_image_sizes();
    }
}