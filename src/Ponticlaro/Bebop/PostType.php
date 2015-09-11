<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Patterns\TrackableObjectAbstract;

class PostType extends TrackableObjectAbstract
{
    /**
     * Required trackable object type
     * 
     * @var string
     */
    protected $__trackable_type = 'post_type';

    /**
     * Required trackable object ID
     * 
     * @var string
     */
    protected $__trackable_id;

    /**
     * Configuration for this post type 
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $config;

    /**
     * Supported features for this post type 
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $features;

    /**
     * Labels for this post type 
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $labels;

    /**
     * Capabilities for this post type 
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $capabilities;

    /**
     * Taxonomies for this post type 
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $taxonomies;

    /**
     * Rewrite configuration
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $rewrite_config;

    /**
     * Map with alias for existing methods
     * 
     * @var array
     */
    protected $methods_alias = array(
        'setPublic'            => 'makePublic',
        'setHasArchive'        => 'archiveEnabled',
        'setShowUi'            => 'showUi',
        'setShowInNavMenus'    => 'showInNavMenus',
        'setShowInMenu'        => 'showInMenu',
        'setShowInAdminBar'    => 'showInAdminBar',
        'replaceSupports'      => 'replaceFeatures',
        'addSupports'          => 'addFeatures',
        'addSupport'           => 'addFeature',
        'removeSupports'       => 'removeFeatures',
        'removeSupport'        => 'removeFeature',
        'getSupports'          => 'getFeatures',
        'setRegisterMetaBoxCb' => 'setMetaboxesCallback',
        'setMapMetaCap'        => 'setMapMetaCapabilities'
    );

    /**
     * Instantiates new post type
     * 
     * @param mixed $name String or array with singular name first and plural name in second
     */
    public function __construct($name)
    {
        // Instantiate configuration object with defaults
        $this->config = Bebop::Collection(array(
            'public'              => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'show_ui'             => true, 
            'query_var'           => true,
            'can_export'          => true
        ));

        // Instantiate features object with defaults
        $this->features = Bebop::Collection(array( 
            'title',
            'editor',
            'revisions'
        )); 

        // Instantiate labels object
        $this->labels = Bebop::Collection();

        // Instantiate capabilities object
        $this->capabilities = Bebop::Collection();

        // Instantiate taxonomies object
        $this->taxonomies = Bebop::Collection();

        // Instantiate rewrite configuration object
        $this->rewrite_config = Bebop::Collection([
            'with_front' => false, // Defaults to 'false' so that we can use the Permalinks menu to change permalinks for the built-in post post-type
        ]);

        // Set post type name
        call_user_func_array(array($this, '__setName'), is_array($name) ? $name : array($name));

        // Set post_type id
        $this->__trackable_id = Bebop::util('slugify', is_array($name) ? $name[0] : $name);

        // Set default labels based on singular and plural names
        $this->__setDefaultLabels();

        // Hook into init action to register post type
        add_action("init", array($this, '__register'), 1);
    }

    /**
     * Sets post type ID
     * 
     * @param  string                     $id
     * @return \Ponticlaro\Bebop\PostType     PostType instance
     */
    public function setId($id)
    {
        if (is_string($id))
            $this->__trackable_id = $id;

        return $this;
    }

    /**
     * Returns post type ID
     * 
     * @return string $id
     */
    public function getId()
    {
        return $this->__trackable_id;
    }

    /**
     * Sets post type labels 
     * 
     * @param  array                      $labels Associative array with post type labels
     * @return \Ponticlaro\Bebop\PostType         PostType instance
     */
    public function setLabels(array $labels = array())
    {
        foreach ($labels as $key => $value) {
            
            $this->setLabel($key, $value);
        }

        return $this;
    }

    /**
     * Sets a single post type label
     * 
     * @param  string                     $key   Label key
     * @param  string                     $value Label value
     * @return \Ponticlaro\Bebop\PostType        PostType instance
     */
    public function setLabel($key, $value)
    {
        if (!is_string($key) || !is_string($value))
            throw new \Exception('PostType label $key and $value arguments must be strings.');

        $this->labels->set($key, $value);

        return $this;
    }

    /**
     * Returns all labels
     * 
     * @return array
     */
    public function getLabels()
    {
        return $this->labels->getAll();
    }

    /**
     * Returns a single label by its key
     * 
     * @param  string $key Label key
     * @return string      Label value
     */
    public function getLabel($key)
    {
        return $this->labels->get($key);
    }

    /**
     * Sets post type description
     * 
     * @param string $description
     */
    public function setDescription($description)
    {
        if (!is_string($description))
             throw new \Exception('PostType description must be a string.');

        $this->config->set('description', $description);

        return $this;
    }

    /**
     * Returns post type description
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->config->get('description');
    }

    /**
     * Sets post type 'public' value
     * 
     * @param  boolean                    $enabled True to enable, false otherwise
     * @return \Ponticlaro\Bebop\PostType          PostType instance
     */
    public function makePublic($enabled = true)
    {
        $this->config->set('public', $enabled);

        return $this;
    }

    /**
     * Checks if post type is public
     * 
     * @return boolean 
     */
    public function isPublic()
    {
        return $this->config->get('public');
    }

    /**
     * Sets post type 'has_archive' value
     * 
     * @param  boolean                    $enabled True to enable, false otherwise
     * @return \Ponticlaro\Bebop\PostType          PostType instance
     */
    public function archiveEnabled($enabled)
    {
        $this->config->set('has_archive', $enabled);

        return $this;
    }

    /**
     * Checks if post type has archive
     * 
     * @return boolean 
     */
    public function hasArchive()
    {
        return $this->config->set('has_archive');
    }

   /**
     * Sets post type 'exclude_from_search' value
     * 
     * @param  boolean                    $enabled True to enable, false otherwise
     * @return \Ponticlaro\Bebop\PostType          PostType instance
     */
    public function setExcludeFromSearch($enabled = true)
    {
        $this->config->set('exclude_from_search', $enabled);

        return $this;
    }

    /**
     * Checks if post type should be included in search results
     * 
     * @return boolean 
     */
    public function isExcludedFromSearch()
    {
        return $this->config->get('exclude_from_search') ?: !$this->config->get('public');
    }

   /**
     * Sets post type 'hierarchical' value
     * 
     * @param  boolean                    $enabled True to enable, false otherwise
     * @return \Ponticlaro\Bebop\PostType          PostType instance
     */
    public function setHierarchical($enabled = true)
    {
        $this->config->set('hierarchical', $enabled);

        return $this;
    }

    /**
     * Checks if post type is hierarchical
     * 
     * @return boolean 
     */
    public function isHierarchical()
    {
        return $this->config->get('hierarchical');
    }

   /**
     * Sets post type 'can_export' value
     * 
     * @param  boolean                    $enabled True to enable, false otherwise
     * @return \Ponticlaro\Bebop\PostType          PostType instance
     */
    public function setExportable($enabled = true)
    {
        $this->config->set('can_export', $enabled);

        return $this;
    }

    /**
     * Checks if post type can be exported
     * 
     * @return boolean 
     */
    public function isExportable()
    {
        return $this->config->get('can_export');
    }

   /**
     * Sets post type 'publicly_queryable' value
     * 
     * @param  boolean                    $enabled True to enable, false otherwise
     * @return \Ponticlaro\Bebop\PostType          PostType instance
     */
    public function setPubliclyQueryable($enabled = true)
    {
        $this->config->set('publicly_queryable', $enabled);

        return $this;
    }

    /**
     * Checks if post type is publicly queryable
     * 
     * @return boolean 
     */
    public function isPubliclyQueryable()
    {
        return $this->config->get('publicly_queryable') ?: $this->config->get('public');
    }

    /**
     * Sets the value for 'show_ui'
     * 
     * @param  string                     $enabled True to enable, false otherwise
     * @return \Ponticlaro\Bebop\PostType          PostType instance
     */
    public function showUi($enabled)
    {
        $this->config->set('show_ui', $enabled);

        return $this;
    }

    /**
     * Sets the value for 'show_in_nav_menus'
     * 
     * @param  string                     $enabled True to enable, false otherwise
     * @return \Ponticlaro\Bebop\PostType          PostType instance
     */
    public function showInNavMenus($enabled)
    {
        $this->config->set('show_in_nav_menus', $enabled);

        return $this;
    }

   /**
     * Sets the value for 'show_in_menu'
     * 
     * @param  string                     $enabled True to enable, false otherwise
     * @return \Ponticlaro\Bebop\PostType          PostType instance
     */
    public function showInMenu($value)
    {
        $this->config->set('show_in_menu', $value);

        return $this;
    }

   /**
     * Sets the value for 'show_in_menu'
     * 
     * @param  string                     $enabled True to enable, false otherwise
     * @return \Ponticlaro\Bebop\PostType          PostType instance
     */
    public function showInAdminBar($show)
    {
        $this->config->set('show_in_admin_bar', $enabled);

        return $this;
    }

    /**
     * Sets menu position
     * 
     * @param  string                     $position
     * @return \Ponticlaro\Bebop\PostType           PostType instance
     */
    public function setMenuPosition($position)
    {
        if (!is_int($position))
            throw new \Exception('PostType menu position must be an integer.');

        $this->config->set('menu_position', $position);

        return $this;
    }

    /**
     * Returns menu position
     * 
     * @return integer
     */
    public function getMenuPosition()
    {
        return $this->config->get('menu_position');
    }

   /**
     * Sets menu icon
     * 
     * @param  string                     $icon
     * @return \Ponticlaro\Bebop\PostType       PostType instance
     */
    public function setMenuIcon($icon)
    {
        if (!is_string($icon))
            throw new \Exception('PostType menu icon must be a string.');

        $this->config->set('menu_icon', $icon);

        return $this;
    }

    /**
     * Returns menu icon
     * 
     * @return string
     */
    public function getMenuIcon()
    {
        return $this->config->get('menu_icon');
    }

   /**
     * Sets capability type
     * 
     * @param  string                     $type
     * @return \Ponticlaro\Bebop\PostType       PostType instance
     */
    public function setCapabilityType($type)
    {
        if (!is_string($type))
            throw new \Exception('PostType capability type must be a string.');

        $this->config->set('capability_type', $type);

        return $this;
    }

    /**
     * Returns capability type
     * 
     * @return string
     */
    public function getCapabilityType()
    {
        return $this->config->get('capability_type');
    }

    /**
     * Replaces capabilities
     * 
     * @param  array                      $capabilities Indexed array with capabilities
     * @return \Ponticlaro\Bebop\PostType               PostType instance
     */
    public function replaceCapabilities(array $capabilities = array())
    {
        $this->capabilities->clear();
        $this->setCapabilities($capabilities);

        return $this;
    }

    /**
     * Sets capabilities
     * 
     * @param  array                      $capabilities Indexed array with capabilities
     * @return \Ponticlaro\Bebop\PostType               PostType instance
     */
    public function setCapabilities(array $capabilities = array())
    {
        foreach ($capabilities as $capability) {
            
            $this->addCapability($capability);
        }

        return $this;
    }

    /**
     * Adds a single capability
     * 
     * @param  string                     $capability
     * @return \Ponticlaro\Bebop\PostType             PostType instance
     */
    public function addCapability($capability)
    {
        if (!is_string($capability))
            throw new \Exception('PostType capability must be a string.');

        if (!$this->capabilities->hasValue($capability))
            $this->capabilities->push($capability);

        return $this;
    }

    /**
     * Removes capabilities
     * 
     * @param  array                      $capabilities Indexed array with capabilities
     * @return \Ponticlaro\Bebop\PostType               PostType instance
     */
    public function removeCapabilities(array $capabilities = array())
    {
        foreach ($capabilities as $capability) {
            
            $this->removeCapability($capability);
        }

        return $this;
    }

    /**
     * Removes a single capability
     * 
     * @param  string                     $capability
     * @return \Ponticlaro\Bebop\PostType             PostType instance
     */
    public function removeCapability($capability)
    {
        if (!is_string($capability))
            throw new \Exception('PostType capability must be a string.');

        if ($this->capabilities->hasValue($capability))
            $this->capabilities->pop($capability);

        return $this;
    }

    /**
     * Returns all capabilities
     * 
     * @return array
     */
    public function getCapabilities()
    {
         return $this->capabilities->getAll();
    }

    /**
     * Sets the value for 'map_meta_cap'
     * 
     * @param  string                     $enabled True to enable, false otherwise
     * @return \Ponticlaro\Bebop\PostType          PostType instance
     */
    public function setMapMetaCapabilities($enabled = true)
    {
        $this->config->set('map_meta_cap', $enabled);

        return $this;
    }

    /**
     * Replaces features
     * 
     * @param  array                      $features Indexed array with features
     * @return \Ponticlaro\Bebop\PostType           PostType instance
     */
    public function replaceFeatures(array $features = array())
    {
        $this->features->clear();
        $this->addFeatures($features);

        return $this;
    }

    /**
     * Adds features, on top of existing ones
     * 
     * @param  array                      $features Indexed array with features
     * @return \Ponticlaro\Bebop\PostType           PostType instance
     */
    public function addFeatures(array $features = array())
    {
        foreach ($features as $feature) {
            
            $this->addFeature($feature);
        }

        return $this;
    }

    /**
     * Adds single feature
     * 
     * @param string $feature
     */
    public function addFeature($feature)
    {
        if (!is_string($feature))
             throw new \Exception('PostType feature must be a string.');

        if (!$this->features->hasValue($feature))
            $this->features->push($feature);

        return $this;
    }

    /**
     * Removes features
     * 
     * @param  array                      $features Indexed array with features
     * @return \Ponticlaro\Bebop\PostType           PostType instance
     */
    public function removeFeatures(array $features = array())
    {
        foreach ($features as $feature) {
            
            $this->removeFeature($feature);
        }

        return $this;
    }

    /**
     * Removes single feature
     * 
     * @param string $feature
     */
    public function removeFeature($feature)
    {
        if (!is_string($feature))
             throw new \Exception('PostType feature must be a string.');

        if ($this->features->hasValue($feature))
            $this->features->pop($feature);

        return $this;
    }

    /**
     * Returns all features
     * 
     * @return array
     */
    public function getFeatures()
    {
        return $this->features->getAll();
    }

    /**
     * Sets the value for 'register_meta_box_cb'
     * 
     * @param  string                     $enabled True to enable, false otherwise
     * @return \Ponticlaro\Bebop\PostType          PostType instance
     */
    public function setMetaboxesCallback($callback)
    {
        if (!is_callable($callback))
             throw new \Exception('PostType metaboxes callback must be callable.');

        $this->config->set('register_meta_box_cb', $callback);

        return $this;
    }

    /**
     * Returns the value for 'register_meta_box_cb'
     * 
     * @return string
     */
    public function getMetaboxesCallback()
    {
        return $this->config->get('register_meta_box_cb');
    }

    /**
     * Replaces taxonomies
     * 
     * @param  array                      $taxonomies Indexed array with taxonomies
     * @return \Ponticlaro\Bebop\PostType             PostType instance
     */
    public function replaceTaxonomies(array $taxonomies = array())
    {
        $this->taxonomies->clear();
        $this->addTaxonomies($taxonomies);

        return $this;
    }

    /**
     * Adds taxonomies
     * 
     * @param  array                      $taxonomies Indexed array with taxonomies
     * @return \Ponticlaro\Bebop\PostType             PostType instance
     */
    public function addTaxonomies(array $taxonomies = array())
    {
        foreach ($taxonomies as $taxonomy) {
            
            $this->addTaxonomy($taxonomy);
        }

        return $this;   
    }

    /**
     * Adds single taxonomy
     * 
     * @param string $taxonomy
     */
    public function addTaxonomy($taxonomy)
    {
        if (!is_string($taxonomy))
             throw new \Exception('PostType taxonomy must be a string.');

        if (!$this->taxonomies->hasValue($taxonomy))
            $this->taxonomies->push($taxonomy);

        return $this;
    }

    /**
     * Removes taxonomies
     * 
     * @param  array                      $taxonomies Indexed array with taxonomies
     * @return \Ponticlaro\Bebop\PostType             PostType instance
     */
    public function removeTaxonomies(array $taxonomies = array())
    {
        foreach ($taxonomies as $taxonomy) {
            
            $this->removeTaxonomy($taxonomy);
        }

        return $this;   
    }

    /**
     * Removes single taxonomy
     * 
     * @param string $taxonomy
     */
    public function removeTaxonomy($taxonomy)
    {
        if (!is_string($taxonomy))
             throw new \Exception('PostType taxonomy must be a string.');

        if ($this->taxonomies->hasValue($taxonomy))
            $this->taxonomies->pop($taxonomy);

        return $this;
    }

    /**
     * Returns all taxonomies
     * 
     * @return array
     */
    public function getTaxonomies()
    {
        return $this->taxonomies->getAll();
    }

    /**
     * Sets the value for 'permalink_epmask'
     * 
     * @param  string                     $epmask
     * @return \Ponticlaro\Bebop\PostType         PostType instance
     */
    public function setPermalinkEpmask($epmask)
    {
        if (!is_string($epmask))
             throw new \Exception('PostType epmask must be a string.');

        $this->config->set('permalink_epmask', $epmask);

        return $this;
    }

    /**
     * Returns the value for 'permalink_epmask'
     * 
     * @return string
     */
    public function getPermalinkEpmask()
    {
        return $this->config->get('permalink_epmask');
    }

    /**
     * Sets the value for 'rewrite'
     * 
     * @param  array                      $args
     * @return \Ponticlaro\Bebop\PostType       PostType instance
     */
    public function setRewrite(array $args = array())
    {
        $this->rewrite_config->set($args);

        return $this;
    }

    /**
     * Sets the value for 'rewrite[slug]'
     * 
     * @param  string                     $slug
     * @return \Ponticlaro\Bebop\PostType       PostType instance
     */
    public function setRewriteSlug($slug)
    {
        if (!is_string($slug))
             throw new \Exception('PostType rewrite slug must be a string.');

        $this->rewrite_config->set('slug', $slug);

        return $this;
    }

    /**
     * Sets the value for 'rewrite[with_front]'
     * 
     * @param  string                     $enabled
     * @return \Ponticlaro\Bebop\PostType          PostType instance
     */
    public function setRewriteWithFront($enabled)
    {
        $this->rewrite_config->set('with_front', $enabled);

        return $this;
    }

    /**
     * Sets the value for 'rewrite[feeds]'
     * 
     * @param  string                     $enabled
     * @return \Ponticlaro\Bebop\PostType         PostType instance
     */
    public function setRewriteFeeds($enabled)
    {
        $this->rewrite_config->set('feeds', $enabled);

        return $this;
    }

    /**
     * Sets the value for 'rewrite[pages]'
     * 
     * @param  string                     $enabled
     * @return \Ponticlaro\Bebop\PostType         PostType instance
     */
    public function setRewritePages($enabled)
    {
        $this->rewrite_config->set('pages', $enabled);

        return $this;
    }

    /**
     * Sets the value for 'rewrite[ep_mask]'
     * 
     * @param  string                     $epmask
     * @return \Ponticlaro\Bebop\PostType         PostType instance
     */
    public function setRewriteEpmask($epmask)
    {
        if (!is_string($epmask))
             throw new \Exception('PostType rewrite ep_mask must be a string.');

        $this->rewrite_config->set('ep_mask', $epmask);

        return $this;
    }

    /**
     * Returns rewrite configuration
     * 
     * @return array
     */
    public function getRewrite()
    {
        return $this->rewrite_config->getAll();
    }

    /**
     * Sets query_var
     * 
     * @param  string                     $query_var
     * @return \Ponticlaro\Bebop\PostType            PostType instance
     */
    public function setQueryVar($query_var)
    {
        if (!is_bool($query_var) && !is_string($query_var))
            throw new \Exception('PostType query_var must be a string or false.');

        $this->config->set('query_var', $query_var);

        return $this;
    }

    /**
     * Returns query_var
     * 
     * @return string
     */
    public function getQueryVar()
    {
        return $this->config->get('query_var');
    }

    /**
     * Applies a register_post_type $args configuration array to this PostType
     * 
     * @param  array $args Same argumeents used to 
     * @return void
     */
    public function applyRawArgs(array $args = array())
    {
        // Intercept labels
        if (isset($args['labels']) && is_array($args['labels'])) {

            $this->setLabels($args['labels']);
            unset($args['labels']);
        }

        // Intercept supports
        if (isset($args['supports']) && is_array($args['supports'])) {

            $this->replaceFeatures($args['supports']);
            unset($args['supports']);
        }

        // Intercept capabilities
        if (isset($args['capabilities']) && is_array($args['capabilities'])) {

            $this->replaceCapabilities($args['capabilities']);
            unset($args['capabilities']);
        }

        // Intercept taxonomies
        if (isset($args['taxonomies']) && is_array($args['taxonomies'])) {

            $this->replaceTaxonomies($args['taxonomies']);
            unset($args['taxonomies']);
        }

        $this->config->set($args);
    }

    /**
     * Checks if the called method is an alias
     * and calls the existing method
     * 
     * @param  string $name Method name
     * @param  array  $args Method arguments
     * @return mixed        Returns current post type instance or method return value
     */
    public function __call($name, $args)
    {
        if (in_array($name, $this->methods_alias))
            return call_user_func_array(array($this, $this->methods_alias[$name]), $args);

        return $this;
    }

    /**
     * Sets both the singular and plural names
     * 
     * @param  string                     $singular Singular name to be set
     * @param  string                     $plural   Plural name to be set
     * @return \Ponticlaro\Bebop\PostType           PostType instance
     */
    protected function __setName($singular, $plural = null)
    {
        $this->__setSingularName($singular);

        if (is_null($plural))
            $plural = $singular . 's';

        $this->__setPluralName($plural);

        return $this;
    }

    /**
     * Sets the singular name
     * 
     * @param  string                     $name Singular name to be set
     * @return \Ponticlaro\Bebop\PostType       PostType instance
     */
    protected function __setSingularName($name)
    {
        if (!is_string($name))
             throw new \Exception('PostType singular name must be a string.');

        $this->config->set('singular_name', $name);

        return $this;
    }

    /**
     * Sets the plural name
     * 
     * @param  string                     $name Plural name to be set
     * @return \Ponticlaro\Bebop\PostType       PostType instance
     */
    protected function __setPluralName($name)
    {
        if (!is_string($name))
             throw new \Exception('PostType plural name must be a string.');

        $this->config->set('plural_name', $name);

        if (!$this->getLabel('menu_name'))
            $this->setLabel('menu_name', $name);

        return $this;
    }

    /**
     * Sets default labels based on singular and plural names for this post type
     * 
     * @return void
     */
    protected function __setDefaultLabels()
    {
        $singular = $this->config->get('singular_name');
        $plural   = $this->config->get('plural_name');
        $labels   = array(
            'name'               => __($plural),
            'singular_name'      => __($singular),
            'menu_name'          => __($plural),
            'all_items'          => __($plural),
            'add_new'            => __('Add '. $singular),
            'add_new_item'       => __('Add new '. $singular), 
            'edit_item'          => __('Edit '. $singular), 
            'new_item'           => __('New '. $singular),
            'view_item'          => __('View '. $singular),
            'search_items'       => __('Search '. $plural),
            'not_found'          => __('There are no '. $plural),
            'not_found_in_trash' => __('There are no '. $plural .' in trash'), 
            'parent_item_colon'  => __('Parent '. $singular . ':') 
        );

        $this->setLabels($labels);
    }

    /**
     * Returns built configuration array
     * 
     * @return array
     */
    public function getFullConfig()
    {
        $config                 = $this->config->getAll();
        $config['labels']       = $this->getLabels();
        $config['supports']     = $this->getFeatures();
        $config['capabilities'] = $this->getCapabilities();
        $config['taxonomies']   = $this->getTaxonomies();
        $config['rewrite']      = $this->getRewrite();

        return $config;
    }

    /**
     * Registers post type
     * 
     * @return void
     */
    public function __register()
    {
        register_post_type($this->getId(), $this->getFullConfig());
    }
}