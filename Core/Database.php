<?php

namespace Core;

class Database
{
    private static $instance = null;
    
    private static $dsn;
    private static $username;
    private static $password;
    
    private function __construct() {}
    private function __clone() {}
    
    public static function setParams($dsn, $username, $password)
    {
        self::$dsn = $dsn;
        self::$username = $username;
        self::$password = $password;
    }
    
    public static function getInstace()
    {
        if (isset(self::$dsn) && !isset(self::$instance))
        {
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                \PDO::ATTR_STRINGIFY_FETCHES => false,
                \PDO::ATTR_EMULATE_PREPARES => false
            ];
            self::$instance = new \PDO(self::$dsn, self::$username, self::$password, $options);
        }
        return self::$instance;
    }
}
