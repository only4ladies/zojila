<?php

/**
 * Handling database connection
 *
 * @author Pramod Kumar Raghav
 */
class dbConnect {

    private $conn;

    function __construct() {        
    }

    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect() {
        include_once dirname(__FILE__) . '/config.php';

        // Connecting to mysql database
        $this->conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Check for database connection error
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

		date_default_timezone_set(TIMEZONE);
        // returing connection resource
        return $this->conn;
    }

}

?>
