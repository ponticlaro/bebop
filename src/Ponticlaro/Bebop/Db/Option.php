<?php

namespace Ponticlaro\Bebop\DB;

use Ponticlaro\Bebop;

class Option
{	
	private $__config;

	private $__config_defaults = array(
		"autosave" => false
	);

	private $__data;

	public function __construct()
	{	
		// Take any necessary actions to make this object usable
		$this->__requiredInitConfig();

		// Set configuration if arguments are passed
		$args = func_get_args();
		if($args) call_user_func_array( array($this, 'initConfig'), $args );

	}

	private function __requiredInitConfig()
	{
		$this->__config = Bebop::Collection( $this->__config_defaults );
		$this->__data   = Bebop::Collection();
	}

	public function initConfig()
	{	
		$args = func_get_args();

		if($args){
			$this->__handleInitConfig($args);
		}

		return $this;
	}

	private function __handleInitConfig($args)
	{

		// handle configuration parameter
		if( !isset($args[0]) )
			throw new \ErrorException('You must pass a configuration parameter');

		if( !is_string($args[0]) && !is_array($args[0]) )
			throw new \ErrorException('Configuration parameter must be a string or array');

		if( is_string($args[0])) {

			$this->setConfig('hook', $args[0]);

		} elseif( is_array($args[0])){

			$this->setConfig($args[0]);

		}

		// Fetch existing data in database, if any
		$this->set( $this->fetch() );

		// Handle data OR autosave parameter
		if( isset($args[1]) ) {

			if ( is_bool($args[1]) ){

				$this->setConfig('autosave', $args[1]);

			} elseif( is_array($args[1]) ){

				$this->set($args[1]);

			}

		}

		// Handle autosave parameter
		if( isset($args[2]) && is_bool($args[2])) {

			$this->setConfig('autosave', $args[2]);

		}

	}

	public function fetch()
	{
		if( Bebop::util('isNetwork') ){
			return get_site_option( $this->getConfig('hook') );

		}else{
			return get_option( $this->getConfig('hook') );

		}	
		
	}

	public function setConfig($key, $value = null)
	{
		$this->__config->set($key, $value);
		return $this;
	}

	public function getConfig($key = null)
	{
		return $this->__config->get($key);
	}

	public function removeConfig($key)
	{
		$this->__config->remove($key);
		return $this;
	}

	public function set($key, $value = null)
	{
		$this->__data->set($key, $value);

		$this->__autosave();

		return $this;
	}

	public function get($key = null)
	{
		return $this->__data->get($key);
	}

	public function getAll()
	{
		return $this->__data->getAll();
	}

	public function remove($key)
	{
		$this->__data->remove($key);

		$this->__autosave();

		return $this;
	}

	public function save()
	{
		if( Bebop::util('isNetwork') ){
			update_site_option( $this->getConfig('hook'), $this->getAll() );

		}else{
			update_option( $this->getConfig('hook'), $this->getAll() );

		}		
		return $this;
	}
	
	public function destroy()
	{
		if( Bebop::util('isNetwork') ){
			delete_site_option( $this->getConfig('hook') );

		}else{
			delete_option( $this->getConfig('hook') );

		}		
	}

	private function __autosave()
	{
		if( $this->getConfig('autosave') ) $this->save();

		return $this;
	}



}