<?php

namespace Ponticlaro\Bebop\DB;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Db;
use Ponticlaro\Bebop\Db\SqlProjection;

class ObjectMeta {

    /**
     * Valid object types with meta
     * 
     * @var array
     */
    protected static $objects_with_meta = array('post', 'user', 'comment');

    /**
     * Object type: post, user or comment
     * 
     * @var string
     */
    protected $object_type;

    /**
     * ID of the target object
     * 
     * @var int
     */
    protected $object_id;

    /**
     * Table that contains meta data for the target object
     * 
     * @var string
     */
    protected $table;

    /**
     * Column that contains the ID of this object
     * 
     * @var string
     */
    protected $id_column;

    /**
     * Options list
     * 
     * @var \Ponticlaro\Bebop\Common\Collection
     */
    protected $options;

    /**
     * Meta columns projection
     * 
     * @var string
     */
    protected $projection;

    /**
     * Creates new instance of the ObjectMeta class
     * 
     * @param string $object_type Object type: post, user or comment
     * @param int    $object_id   Object ID
     */
    public function __construct($object_type, $object_id, array $options = array())
    {
        // Validate object type
        if (!is_string($object_type) || !in_array($object_type, self::$objects_with_meta))
            throw new \UnexpectedValueException("Object type needs to be one of the following strings: post, user or comment");
        
        // Validate object ID
        if (intval($object_id) == 0)
            throw new \UnexpectedValueException("Object ID needs to be an integer");

        global $wpdb;

        // Sotre target object details
        $this->object_type       = $object_type;
        $this->object_id         = (int) $object_id;
        $this->table             = $wpdb->prefix . $object_type .'meta';
        $this->id_column         = $object_type .'_id';
        
        // Default options
        $default_options = array(
            'projection' => null
        );

        // Merge default options with user input
        $options = array_merge($default_options, $options);

        // Set options
        $this->options = Bebop::Collection($options); 

        // Handle projection
        if ($this->options->get('projection') instanceof SqlProjection) {
            
            $this->projection = $this->options->get('projection');
        }
    }

    /**
     * Returns all meta values for the target meta key
     * 
     * @param  string $meta_key Target meta key
     * @return array            Array of meta values
     */
    public function getAll($meta_key)
    {
        if (!is_string($meta_key)) return null;

        // Get DB connection
        $db = Db::getConnection();

        // Set default parameters
        $fields = 'meta_value';
        $class  = null;

        // Determine which fields to get
        if ($this->projection) {
           
            $fields = $this->projection->getSql();
            $class  = $this->projection->getClass();
        }

        // Select SQL
        $sql = "SELECT
                    $fields
                FROM
                    $this->table
                WHERE
                    $this->id_column = ?
                AND
                    meta_key = ?
                ORDER BY
                    meta_id ASC
                ";

        // Set SQL replacements
        $sql_replacements = array(
            $this->object_id,
            $meta_key
        );

        // Execute query
        $stmt    = $db->prepare($sql);
        $success = $stmt->execute($sql_replacements);
        $items   = $stmt->fetchAll($class ? \PDO::FETCH_CLASS : \PDO::FETCH_COLUMN, $class);

        // Return meta items
        return $items;
    }

    /**
     * Returns a single meta value using its meta_id
     * 
     * @param  string                                        $meta_key Target meta key
     * @param  int                                           $meta_id  Target meta id
     * @return \Ponticlaro\Bebop\Resources\Models\ObjectMeta           Meta data entry
     */
    public function get($meta_key, $meta_id)
    {
        if (!is_string($meta_key) || intval($meta_id) == 0) return null;

        // Get DB connection
        $db = Db::getConnection();

        // Set default parameters
        $fields = 'meta_value';
        $class  = null;

        // Determine which fields to get
        if ($this->projection) {
           
            $fields = $this->projection->getSql();
            $class  = $this->projection->getClass();
        }

        // Set SQL
        $sql = "SELECT
                    $fields
                FROM
                    $this->table
                WHERE
                    $this->id_column = ?
                AND
                    meta_key = ?
                AND
                    meta_id = ?
                LIMIT 
                    1
                ";

        // Set SQL replacements
        $sql_replacements = array(
            $this->object_id,
            $meta_key,
            $meta_id
        );

        // Execute query
        $stmt    = $db->prepare($sql);
        $stmt->setFetchMode($class ? \PDO::FETCH_CLASS : \PDO::FETCH_COLUMN, $class);
        $success = $stmt->execute($sql_replacements);
        $item    = $stmt->fetch();

        // Return meta item (or null)
        return $item ?: null;
    }

    /**
     * Used to add a single meta value on the target meta_key
     * 
     * @param  string                                        $meta_key       Target meta key
     * @param  mixed                                         $meta_value     Value to be store in database
     * @param  string                                        $storage_method Store arrays or objects either as JSON or serialized strings
     * @return \Ponticlaro\Bebop\Resources\Models\ObjectMeta                 Meta data entry
     */
    public function add($meta_key, $meta_value, $storage_method = 'json')
    {   
        if (!is_string($meta_key) || !is_string($storage_method)) return null;

        // Get DB connection
        $db = Db::getConnection();

        // Set SQL
        $sql = "INSERT INTO $this->table ($this->id_column, meta_key, meta_value)
                VALUES (?, ?, ?)";

        // Set SQL replacements
        $sql_replacements = array(
            $this->object_id,
            $meta_key,
            self::__applyStorageMethod($meta_value, $storage_method)
        );

        // Execute query
        $stmt     = $db->prepare($sql);
        $success  = $stmt->execute($sql_replacements);
        $new_item = self::get($meta_key, $db->lastInsertid());

        // Return new item (or null)
        return $new_item;
    }

    /**
     * Used to update a single meta value on the target meta_key
     * 
     * @param  string                                        $meta_key       Target meta key
     * @param  int                                           $meta_id        Target meta id
     * @param  mixed                                         $meta_value     Value to be store in database
     * @param  string                                        $storage_method Store arrays or objects either as JSON or serialized strings
     * @return \Ponticlaro\Bebop\Resources\Models\ObjectMeta                 Meta data entry
     */
    public function update($meta_key, $meta_id, $meta_value, $storage_method = 'json')
    {
        if (!is_string($meta_key) || intval($meta_id) == 0 || !is_string($storage_method)) return null;

        // Get DB connection
        $db = Db::getConnection();

        // Start transaction
        $db->beginTransaction();

        // Update SQL
        $sql = "UPDATE
                    $this->table
                SET
                    meta_value = ?
                WHERE
                    $this->id_column = ?
                AND
                    meta_key = ?
                AND
                    meta_id = ?
                ";
        
        // Set SQL replacements
        $sql_replacements = array(
            self::__applyStorageMethod($meta_value, $storage_method),
            $this->object_id,
            $meta_key,
            $meta_id
        );

        // Execute query
        $stmt    = $db->prepare($sql);
        $success = $stmt->execute($sql_replacements);

        // Commit transaction
        $db->commit();

        // Return updated entry (or null)
        return self::get($meta_key, $meta_id);
    }

    /**
     * Replaces all entries for the target meta_key
     * 
     * @param  string $meta_key       Target meta key
     * @param  string $storage_method Store arrays or objects either as JSON or serialized strings
     * @return array                  List of added entries
     */
    public function replace($meta_key, array $meta_values = array(), $storage_method = 'json')
    {
        if (!is_string($meta_key) || !$meta_values || !is_string($storage_method)) return null;

        // Get DB connection
        $db = Db::getConnection();

        // Start transaction
        $db->beginTransaction();

        // Delete all entries
        self::deleteAll($meta_key);

        // Set initial SQL
        $sql = "INSERT INTO $this->table ($this->id_column, meta_key, meta_value) VALUES";

        // Set SQL replacements
        $sql_replacements = array();

        // Loop through meta values
        $counter = 0;

        foreach ($meta_values as $meta_value) {
            
            $counter++;

            // Prepend comma if this is not the first value
            if ($counter !== 1) $sql .= ",";

            // Add SQL placeholders for this meta value
            $sql .= " (?, ?, ?)";

            // Add SQL replacements for this meta value
            $sql_replacements[] = $this->object_id;
            $sql_replacements[] = $meta_key;
            $sql_replacements[] = self::__applyStorageMethod($meta_value, $storage_method);
        }

        // Execute query
        $stmt    = $db->prepare($sql);
        $success = $stmt->execute($sql_replacements);

        // Rollback on failure
        if (!$success) {

            $db->rollBack();
            throw new \Exception("Failed to replace post meta for object $this->object_id");
        }

        // Commit transaction
        $db->commit();

        // Return all items
        return self::getAll($meta_key);
    }

    /**
     * Used to delete a single meta value on the target meta_key
     * 
     * @param  string $meta_key       Target meta key
     * @param  int    $meta_id        Target meta id
     * @return array                  Remaining items for target meta key
     */
    public function delete($meta_key, $meta_id)
    {
        if (!is_string($meta_key) || intval($meta_id) == 0) return null;

        // Get DB connection
        $db = Db::getConnection();

        // Set SQL
        $sql = "DELETE FROM
                    $this->table
                WHERE
                    $this->id_column = ?
                AND
                    meta_key = ?
                AND
                    meta_id = ?
                ";

        // Set SQL replacements
        $sql_replacements = array(
            $this->object_id,
            $meta_key,
            $meta_id
        );

        // Execute query
        $stmt    = $db->prepare($sql);
        $success = $stmt->execute($sql_replacements);

        // Return existing entries
        return self::getAll($meta_key);
    }

    /**
     * Used to delete a single meta value on the target meta_key
     * 
     * @param  string $meta_key Target meta key
     * @return array            Remaining items for target meta key
     */
    public function deleteAll($meta_key)
    {
        if (!is_string($meta_key)) return null;

        // Get DB connection
        $db = Db::getConnection();

        // Set SQL
        $sql = "DELETE FROM
                    $this->table
                WHERE
                    $this->id_column = ?
                AND
                    meta_key = ?
                ";

        // Set SQL replacements
        $sql_replacements = array(
            $this->object_id,
            $meta_key
        );

        // Execute query
        $stmt    = $db->prepare($sql);
        $success = $stmt->execute($sql_replacements);

        // Return existing entries
        return self::getAll($meta_key);
    }

    /**
     * Function that converts arrays and objects into the desired storage format
     * 
     * @param  mixed  $data   Data to be converted
     * @param  string $method Storage method: json or serialize
     * @return mixed          Converted array/object
     */
    private static function __applyStorageMethod($data, $method = 'json')
    {
        // Return raw data if it is not either an array or an object
        if (!is_array($data) && !is_object($data)) return $data;

        // Return converted array or object
        return $method == 'json' ? json_encode($data) : serialize($data);
    } 
}