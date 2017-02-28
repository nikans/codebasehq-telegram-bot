<?php

namespace CodebasehqTelegramBot\Database;

use mysqli, mysqli_result, exception;

require_once 'vendor/autoload.php';
require_once 'config/database.php';
	
class Connection extends mysqli
{
    protected static $instance;
    protected static $options = array();

    private function __construct() {
        $o = self::$options;

        // turn off error reporting
        //mysqli_report(MYSQLI_REPORT_OFF);
		
        // connect to database
        parent::__construct(
            isset($o['host'])   ? $o['host']   : DB_HOST,
            isset($o['user'])   ? $o['user']   : DB_USER,
            isset($o['pass'])   ? $o['pass']   : DB_PASSWORD,
            isset($o['dbname']) ? $o['dbname'] : DB_NAME,
            isset($o['port'])   ? $o['port']   : DB_PORT,
            isset($o['sock'])   ? $o['sock']   : false
        );

        // check if a connection established
        if( mysqli_connect_errno() ) {
            throw new exception(mysqli_connect_error(), mysqli_connect_errno());
        }

        $this->query("/*!40101 SET NAMES 'utf8' */");
    }

    public static function getInstance() {
        if( !self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function setOptions( array $opt ) {
        self::$options = array_merge(self::$options, $opt);
    }

/*
    public function query($query) {
        if( !$this->real_query($query) ) {
            throw new exception( $this->error, $this->errno );
        }

        $result = new mysqli_result($this);
        return $result;
    }
*/

//    public function prepare($query) {
//        $stmt = new mysqli_stmt($this, $query);
//        return $stmt;
//    }
}
