<?php
/**
 * Created by PhpStorm.
 * User: dylanbui
 * Date: 11/24/15
 * Time: 12:18 PM
 */

class DbMysqli implements iDatabase
{
    private $mysqli, $stmt;

    public function __construct($hostname, $port, $username, $password, $database)
    {
        try {
            $this->mysqli = new mysqli($hostname, $username, $password, $database, $port);
        } catch (Exception $e) {
            echo 'ERROR: ' . $e->getMessage();
        };
    }

    public function errno()
    {
        return $this->mysqli->errno;
    }

    public function error()
    {
        return $this->mysqli->error_list;
    }

    public function escape($value)
    {
        return $this->mysqli->real_escape_string($value);
    }

    public function query($sql, $data = array())
    {
        try {
            $this->stmt = $this->mysqli->prepare($sql);
            foreach ($data as $item)
                $this->stmt->bind_param('s', $item);

            $result = $this->stmt->fetch();
        } catch (Exception $e) {
            echo 'ERROR: ' . $e->getMessage();
        };

        return $result;
    }

    public function insert($sql, $data = array())
    {

    }

    public function update($sql, $data = array())
    {

    }

    public function delete($sql, $data = array())
    {

    }

    public function replace($sql, $data = array())
    {

    }

    public function countAffected()
    {
        return $this->mysqli->affected_rows();
    }

    public function getLastId()
    {
        return $this->mysqli->insert_id;
    }

    public function close()
    {
        $this->stmt = null;
        return $this->mysqli->close();
    }
}