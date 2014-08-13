<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Helpers\MetaboxData;
use Ponticlaro\Bebop\Patterns\TrackableObjectAbstract;

class Metabox extends TrackableObjectAbstract
{
    /**
     * Required trackable object type
     * 
     * @var string
     */
    protected $__trackable_type = 'metabox';

    /**
     * Required trackable object ID
     * 
     * @var string
     */
    protected $__trackable_id;

    /**
     * Configuration for this metabox 
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $config;

    /**
     * List of post types that should have this metabox
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $post_types;

    /**
     * List of form field names contained in the output of callback function
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $meta_fields;

    /**
     * List of data for each meta fields
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $data;

    /**
     * Instantiates new metabox
     * 
     * @param string $title      Metabox title
     * @param mixed  $post_types Post types this metabox should be assigned to
     * @param mixed  $callback   Function to display this metabox
     */
    public function __construct($title, $post_types = null, $callback = null)
    {
        // Default configuration
        $default_config = array(
            'context'       => 'normal',
            'priority'      => 'default',
            'callback_args' => array()
        );

        // Set basic structures
        $this->config      = Bebop::Collection($default_config);
        $this->post_types  = Bebop::Collection()->disableDottedNotation();
        $this->meta_fields = Bebop::Collection()->disableDottedNotation();
        $this->data        = new MetaboxData;

        // Set Title
        $this->setTitle($title);

        // Set Post types
        if (!is_null($post_types)) {
            
            if (is_string($post_types) || 
                is_object($post_types) && $post_types instanceof \Ponticlaro\Bebop\PostType) {
                
                $this->addPostType($post_types);
            }

            elseif (is_array($post_types)) {
                
                $this->setPostTypes($post_types);
            }

            else {

                throw new \Exception('Metabox $post_types argument must be either a string, array or a \Ponticlaro\Bebop\PostType instance.');
            }
        }

        // Set callback
        if (!is_null($callback))
            $this->setCallback($callback);

        // Function to save metabox data
        add_action('save_post', array($this, '__saveMeta'));

        // Register a metabox for each post type
        add_action("add_meta_boxes", array($this, '__register'));
    }

    /**
     * Sets metabox ID
     * 
     * @param string $id
     */
    public function setId($id)
    {
        if (is_string($id))
            $this->__trackable_id = $id;

        return $this;
    }

    /**
     * Returns metabox ID
     * 
     * @return string $id
     */
    public function getId()
    {
        return $this->__trackable_id;
    }

    /**
     * Sets metabox title
     * 
     * @param string $title
     */
    public function setTitle($title)
    {
        if (!is_string($title))
            throw new \Exception('Metabox $title argument must be a string.');

        $this->config->set('title', $title);
        $this->setId(Bebop::util('slugify', $title));

        return $this;
    }

    /**
     * Returns metabox title
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->config->get('title');
    }

    /**
     * Sets metabox callback
     * 
     * @param callable
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback))
            throw new \Exception('Metabox $callback argument must be callable.');

        $this->config->set('callback', $callback);

        return $this;
    }

    /**
     * Returns metabox callback
     * 
     * @return callable
     */
    public function getCallback()
    {
        return $this->config->get('callback');
    }

    /**
     * Sets posts types that should have this metabox
     * 
     * @param array $post_types List containing strings and/or \Ponticlaro\Bebop\PostType instances
     */
    public function setPostTypes(array $post_types = array())
    {
        foreach ($post_types as $post_type) {
            
            $this->addPostType($post_type);
        }

        return $this;
    }

    /**
     * Adds a single post type
     * 
     * @param mixed $post_type Post type name or \Ponticlaro\Bebop\PostType instances
     */
    public function addPostType($post_type)
    {
        if (is_string($post_type)) {

            $this->post_types->push($post_type);

        } elseif(is_object($post_type) && is_a($post_type, 'Ponticlaro\Bebop\PostType')) {

            $this->post_types->push($post_type->getId());
        }

        return $this;
    }

    /**
     * Removes a single post type
     * 
     * @param mixed $post_type Post type name or \Ponticlaro\Bebop\PostType instances
     */
    public function removePostType($post_type)
    {
        $this->post_types->pop($post_type);

        return $this;
    }

    /**
     * Removes all post types
     * 
     */
    public function clearPostTypes()
    {
        $this->post_types->clear();

        return $this;
    }

    /**
     * Returns all post types
     * 
     * @return array List of post types will contain this metabox
     */
    public function getPostTypes()
    {
        return $this->post_types->getAll();
    }

    /**
     * Sets context
     * 
     * @param string $context
     */
    public function setContext($context)
    {
        if (!is_string($context))
            throw new \Exception('Metabox context must be a string.');

        $this->config->set('context', $context);

        return $this;
    }

    /**
     * Returns context
     * 
     * @return string
     */
    public function getContext()
    {
        return $this->config->get('context');
    }

    /**
     * Sets priority
     * 
     * @param string $priority
     */
    public function setPriority($priority)
    {
        if (!is_string($priority))
            throw new \Exception('Metabox priority must be a string.');

        $this->config->set('priority', $priority);

        return $this;
    }

    /**
     * Returns priority
     * 
     * @return string
     */
    public function getPriority()
    {
        return $this->config->get('priority');
    }

    /**
     * Sets callback arguments
     * 
     * @param array $args
     */
    public function setCallbackArgs(array $args = array())
    {
        if ($args)
            $this->config->set('callback_args', $args);

        return $this;
    }

    /**
     * Returns callback arguments
     * 
     * @return array
     */
    public function getCallbackArgs()
    {
        return $this->config->get('callback_args');
    }

    /**
     * Sets metafields to be persisted
     * 
     * @param array $fields
     */
    public function setMetaFields(array $fields = array())
    {
        $this->meta_fields->pushList($fields);

        return $this;
    }

    /**
     * Sets a single metafield to be persisted
     * 
     * @param string $field
     */
    public function addMetaField($field)
    {
        if (!is_string($field))
            throw new \Exception('Metabox meta field must be a string.');

        $this->meta_fields->push($field);

        return $this;
    }

    /**
     * Removes a single metafield from being persisted
     * 
     * @param string $field
     */
    public function removeMetaField($field)
    {
        if (!is_string($field))
            throw new \Exception('Metabox meta field must be a string.');

        $this->meta_fields->pop($field);

        return $this;
    }

    /**
     * Gets field names from callback function
     * 
     */
    private function __setMetaFields()
    {   
        // Only execute if there are no manually defined meta fields
        if (empty($this->meta_fields->getAll())) {

            $function = $this->getCallback();
            $args     = array($this->data, new \WP_Post(new \stdClass), $this);
            $names    = Bebop::util('getControlNamesFromCallable', $function, $args);

            if ($names)
                $this->meta_fields->pushList($names);
        }
    }

    /**
     * Wrapper that executes callback 
     * 
     * @param  \WP_Post                  $post    Post being edited
     * @param  \Ponticlaro\Bebop\Metabox $metabox This metabox instance
     * @return void
     */
    public function __callbackWrapper($post, $metabox)
    {   
        $callback = $this->getCallback();

        if ($callback){

            $id = $this->getId();

            // Add nonce field for security
            wp_nonce_field('metabox_'. $id .'_saving_meta', 'metabox_'. $id .'_nonce');

            // Get fields from callback function
            $this->__setMetaFields();

            $meta_fields = $this->meta_fields->getAll();

            var_dump($meta_fields);

            if ($meta_fields) {

                $meta = Bebop::PostMeta($post->ID);

                foreach ($meta_fields as $meta_field) {

                    $this->data->set($meta_field, $meta->getAll($meta_field));
                }
            }

            // Execute callback
            call_user_func_array($callback, array($this->data, $post, $metabox));
        }
    }

    /**
     * Registers this metabox for each post types it was assigned to
     * 
     * @return void
     */
    public function __register()
    {
        foreach ($this->getPostTypes() as $post_type) {

            add_meta_box( 
                $this->getId(),
                $this->getTitle(),
                array($this, '__callbackWrapper'),
                $post_type, 
                $this->getContext(),
                $this->getPriority(), 
                $this->getCallbackArgs()
            );
        }
    }

    /**
     * Saves meta data
     * 
     * @param  int  $post_id ID of the post currently being edited
     * @return void
     */
    public function __saveMeta($post_id) 
    {
        $id         = $this->getId();
        $nonce_name = 'metabox_'. $id .'_nonce';
        $nonce      = isset($_POST[$nonce_name]) ? $_POST[$nonce_name] : '';

        // Check if $_POST is not empty and nonce is there 
        if (!empty($_POST) && wp_verify_nonce($nonce, 'metabox_'. $id .'_saving_meta')) {
            
            $post = get_post($post_id);

            if (isset($_POST['post_type']) && $_POST['post_type'] == $post->post_type ) {

                // Get out if current post is in the middle of an auto-save
                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

                // Get out if current user cannot edit this post
                if (!current_user_can('edit_post', $post_id)) return $post_id;

                // Get fields from callback function
                $this->__setMetaFields();

                foreach($this->meta_fields->getAll() as $field) {

                    $value = isset($_POST[$field]) ? $_POST[$field] : '';

                    // Empty values
                    if (!$value) {
                        
                        delete_post_meta($post_id, $field);
                    }

                    // Arrays
                    elseif (is_array($value)) {
                        
                        // Delete all entries
                        delete_post_meta($post_id, $field);

                        foreach ($value as $v) {

                            // Add single entry with same meta_key
                            add_post_meta($post_id, $field, $v);
                        }
                    }

                    // Strings, booleans, etc
                    else {

                        update_post_meta($post_id, $field, $value);
                    }
                }
            }
        }
    }
}