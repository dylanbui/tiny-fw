<?php
/**
 * Created by PhpStorm.
 * User: dylanbui
 * Date: 11/24/15
 * Time: 12:18 PM
 */

/**
 *
 * @Lite weight Database abstraction layer
 * @Singleton to create database connection
 *
 *
 */

interface iDatabase
{
    public function __construct($hostname, $port, $username, $password, $database);

    public function selectOneRow($sql, $data = array());
    public function query($sql, $data = array());
    public function insert($sql, $data = array());
    public function update($sql, $data = array());
    public function delete($sql, $data = array());
    public function replace($sql, $data = array());

    public function escape($value);
    public function countAffected();
    public function getLastId();
    public function close();

//    function transactionBegin();
//    function transactionCommit();
//    function transactionRollback();
}


class Db
{

    /**
     * Holds an array insance of self
     * @var $instance
     */
    private static $instances = array();

    /**
     *
     * the constructor is set to private so
     * so nobody can create a new instance using new
     *
     */
    private function __construct()
    {
    }

    /**
     *
     * Return DB instance or create intitial connection
     *
     * @return object (PDO)
     *
     * @access public
     *
     */
    public static function getInstance($config_name = 'database_master')
    {
        if (!isset(self::$instances[$config_name]))
        {
            $config = Config::getInstance();
            $db_driver = $config->config_values[$config_name]['db_driver'];
            $hostname = $config->config_values[$config_name]['db_hostname'];
            $db_name = $config->config_values[$config_name]['db_name'];
            $db_password = $config->config_values[$config_name]['db_password'];
            $db_username = $config->config_values[$config_name]['db_username'];
            $db_port = $config->config_values[$config_name]['db_port'];

            $file = __SITE_PATH . '/lib/core/database/'.$db_driver . '.db.php';

            if (file_exists($file)) {
                require_once($file);
                $class = 'Db'.ucfirst($db_driver);
                self::$instances[$config_name] = new $class($hostname, $db_port, $db_username, $db_password, $db_name);
            } else {
                exit('Error: Could not load database driver type ' . $db_driver . '!');
            }
        }
        return self::$instances[$config_name];
    }


    /**
     *
     * Like the constructor, we make __clone private
     * so nobody can clone the instance
     *
     */
    private function __clone()
    {
    }

} // end of class