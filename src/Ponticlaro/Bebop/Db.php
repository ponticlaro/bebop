<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Db\Query;
use Ponticlaro\Bebop\Db\WpQueryEnhanced;

class Db {

	/**
	 * Current and only instance of this class
	 * 
	 * @var Ponticlaro\Bebop\Db
	 */
	private static $instance;

	/**
	 * Current PDO instance 
	 * 
	 * @var PDO
	 */
	protected static $pdo;

	/**
	 * Returns a new instance of this class
	 * 
	 * @return Ponticlaro\Bebop\Db
	 */
	public static function getInstance()
	{	
		if (is_null(static::$instance)) static::$instance = new static;

		return static::$instance;
	}

	/**
	 * Sets PDO connection
	 * 
	 * @param PDO $pdo
	 */
	public static function setConnection(\PDO $pdo)
	{
		self::$pdo = $pdo;
	}

	/**
	 * Returns current pdo connection
	 * 
	 * @return PDO
	 */
	public static function getConnection()
	{
		if (is_null(self::$pdo)) self::$pdo = self::__getPDO();

		return self::$pdo;
	}

	/**
	 * Returns PDO connection with wp-config.php database configuration
	 *  
	 * @return PDO
	 */
	protected static function __getPDO()
	{
		// Get connection details
		$dsn      = 'mysql:dbname=' . DB_NAME . ";host=" . DB_HOST .';charset='. DB_CHARSET;
		$attempts = 10;
		$pdo      = null;

		while ($attempts && !$pdo instanceof \PDO) {

			try {

				$attempts--;

				// Connection attempt
				$options = array(
					\PDO::ATTR_PERSISTENT => true
				);

				$pdo = new \PDO($dsn, DB_USER, DB_PASSWORD, $options);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

				// A working connection has been obtained, terminate the loop
				$attempts = 0;

			} catch (\PDOException $e) {

                // Throw exception if there are no more retries
				if (!$attempts) throw $e;

				// Wait for half a second before trying again
				usleep(500000);
			}
		}

		return $pdo;
	}

	/**
	 * Returns a query object for the target subject
	 * 
	 * @param  string $subject 
	 * @return mixed
	 */
	public static function query($subject = 'posts')
	{
		switch ($subject) {

			case 'posts':
			default:

				return new Query();
				break;
		}
	}

	/**
	 * Returns an instance of WpQueryEnhanced
	 * 
	 * @param  array                               $args    Query Arguments
	 * @param  array                               $options WpQueryEnhanced options
	 * @return Ponticlaro\Bebop\Db\WpQueryEnhanced
	 */
	public static function wpQuery(array $args = array(), array $options = array())
	{
		return new WpQueryEnhanced($args, $options);
	}
}