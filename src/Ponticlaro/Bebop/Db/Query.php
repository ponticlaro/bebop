<?php 

namespace Ponticlaro\Bebop\Db;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Db;
use Ponticlaro\Bebop\Db\Query\Arg;
use Ponticlaro\Bebop\Db\Query\ArgFactory;

class Query {

    /**
     * List of arguments
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    private $args;

    /**
     * Current argument being worked on
     * 
     * @var Ponticlaro\Bebop\Db\Query\Arg
     */
    private $current_arg;

    /**
     * Query Results
     * @var array
     */
    private $query_results = array();

    /**
     * List of arguments
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    private $query_meta;

    /**
     * Flag to check if query was already used to returns retults or not
     * 
     * @var boolean
     */
    private $was_executed = false;

    /**
     * Creates new query instance
     * 
     */
    public function __construct()
    {
        $this->args = Bebop::Collection();
    }

    /**
     * Handles all the logic needed to:
     * - create new argument
     * - execute action on current argument
     * - execute action on current argument child
     * - get existing parent argument if it needs to add another child item
     * 
     * @param  string                        $name Method name
     * @param  array                         $args Method arguments
     * @return use Ponticlaro\Bebop\Db\Query       Query instance
     */
    public function __call($name, $args)
    {
        $name = strtolower($name);

        // Check current arg for method
        if (!is_null($this->current_arg) && method_exists($this->current_arg, $name)) {

            call_user_func_array(array($this->current_arg, $name), $args);
        }

        // Check if:
        // - current arg is a parent 
        // - its current child have the method
        // - its current child action is still available to be executed
        elseif (!is_null($this->current_arg) && 
                $this->current_arg->isParent() && 
                method_exists($this->current_arg->getCurrentChild(), $name) && 
                $this->current_arg->getCurrentChild()->actionIsAvailable($name)) {

            call_user_func_array(array($this->current_arg->getCurrentChild(), $name), $args);
        }

        // Check if:
        // - current arg is a parent 
        // - method name matches current arg factory ID
        elseif (!is_null($this->current_arg) && 
                $this->current_arg->isParent() &&
                ArgFactory::getInstanceId($this->current_arg) == $name) {

            $this->__addArgChild($this->current_arg, $args);
        }

        // Check if a parent arg is already instantiated for the target method $name
        elseif ($this->args->hasKey($name)) {

            $this->current_arg = $this->args->get($name);

            $this->__addArgChild($this->current_arg, $args);
        }

        // Check for manufacturable argument class
        elseif (ArgFactory::canManufacture($name)) {

            // Save current arg, if there is one
            $this->__collectCurrentArg();

            // Create new arg
            $arg = ArgFactory::create($name, $args);

            // If it is a parent arg, save it immediatelly
            if ($arg->isParent())
                $this->args->set($name, $arg);

            // Store new arg as current
            $this->current_arg = $arg;
        }

        return $this;
    }

    /**
     * Returns all meta info for the executed query
     * 
     * NOTE: Will only contain data after executing the query
     * 
     * @return object
     */
    public function getMeta()
    {
        return $this->query_meta;
    }

    /**
     * Returns the args array needed to query for posts
     * 
     * @return array
     */
    public function getArgs()
    {   
        // Save current arg, if there is one
        $this->__collectCurrentArg();

        $args = array();

        foreach ($this->args->getAll() as $arg) {

            if ($arg->getValue()) {

                $value = $arg->getValue();

                if ($arg->hasMultipleKeys()) {
                    
                    foreach ($value as $k => $v) {
                        
                        if (is_string($k)) {
                            
                            $args[$k] = $v;
                        }
                    }
                }

                elseif ($arg->isChild()) {

                    $parent_key = $arg->getParentKey(); 

                    if (!isset($args[$parent_key])) 
                        $args[$parent_key] = array();

                    $args[$parent_key][] = $arg->getValue();
                }

                else {

                    $args[$arg->getKey()] = $value;
                }
            }
        }

        return $args;
    }

    /**
     * Returns posts by ID
     * 
     * @param  mixed   $ids        Single ID or array of IDs
     * @param  bool    $keep_order True if posts order should match the order of $ids, false otherwise
     * @return mixed               Single WP_Post object or array of WP_Post objects
     */
    public function find($ids, $keep_order = true)
    {   
        // Add placeholder for data to return
        $data = null;

        if (is_numeric($ids)) {
                    
            $posts = $this->post(array($ids))->ppp(1)->findAll();
            $data  = $posts && $posts[0] instanceof \WP_Post ? $posts[0] : null;
        }

        elseif (is_array($ids)) {

            // Get posts
            $data = $this->post($ids)->ppp(count($ids))->findAll();

            // Make sure posts order match IDs order
            if ($data && $keep_order) {
                
                $ordered_posts = array();

                foreach ($ids as $key => $id) {
                    
                    foreach ($data as $post) {
                        
                        if ($post instanceof \WP_Post && $post->ID == $id) {
                            
                             $ordered_posts[$key] = $post;
                        }
                    }
                }

                $data = $ordered_posts;
            }
        }

        // Mark query as executed
        $this->was_executed = true;

        return $data;
    }

    /**
     * Finds posts with the current query
     * 
     * @param  mixed $args Optional arguments. Could be array of query args to be merged or post ID
     */
    public function findAll(array $args = array())
    {
        $query_args = $this->getArgs();

        // Merge user input args with 
        if (is_array($args))
            $query_args = array_merge($query_args, $args);

        // Execute query
        $data = Db::wpQuery($query_args)->setOption('with_meta', true)->execute();

        // Save query items
        if (isset($data['items']))
            $this->query_results = $data['items'];
        
        // Save query meta
        if (isset($data['meta']))
            $this->query_meta = (object) $data['meta'];

        // Mark query as executed
        $this->was_executed = true;

        return $this->query_results;
    }

    /**
     * Checks if this query instance was already used to return results
     * 
     * @return bool True is already executed, false otherwise
     */
    public function wasExecuted()
    {
        return $this->was_executed;
    }

    /**
     * Collects and nullifies the current argument
     *  
     * @return void
     */
    private function __collectCurrentArg()
    {
        if (!is_null($this->current_arg)) {

            // Store unique arg with a key
            if ($this->current_arg->isParent()) {
                
                $this->args->set(ArgFactory::getInstanceId($this->current_arg), $this->current_arg);
            }

            // Push non-unique arg to args collection
            else {

                $this->args->push($this->current_arg);
            }

            // Making sure this arg is not collected again by mistake
            $this->current_arg = null;
        }
    }

    /**
     * Adds child to target argument instance
     * 
     * @param  Arg    $arg  Parent argument instance
     * @param  array  $args Arguments for new argument child item
     * @return void
     */
    private function __addArgChild(Arg $arg, array $args = array())
    {
        call_user_func_array(array($arg, 'addChild'), $args);
    }
}