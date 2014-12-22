<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Pramod Kumar Raghav
 *
 */
class playStoreService {

    private $conn;
	private $dbConnect;
	private $tablename;

    function __construct() {
        require_once dirname(__FILE__) . '/dbConnect.php';
		require_once dirname(__FILE__) . '/passHash.php';


        // opening db connection
        $this->dbConnect = new dbConnect();
		$this->tablename = "play_store";
    }



	/**
     * Updating user info
     * @param String $pic User mobile
     */
    public function getProductLicenseKey () {

		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "SELECT play_store_public_key FROM $this->tablename WHERE id = 1");
        $this->throwExceptionOnError();


        mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_bind_result($stmt, $item->key);
		$this->throwExceptionOnError();

		mysqli_stmt_fetch($stmt);
		$this->throwExceptionOnError();


        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

		return $item;
    }

	/**
     * Updating user info
     * @param String $pic User mobile
     */
    public function getTrialPeriod () {

		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "SELECT trial_period FROM $this->tablename WHERE id = 1");
        $this->throwExceptionOnError();


        mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_bind_result($stmt, $trial_period);
		$this->throwExceptionOnError();

		mysqli_stmt_fetch($stmt);
		$this->throwExceptionOnError();


        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

		return $trial_period;
    }

	/**
     * Updating user info
     * @param String $pic User mobile
     */
    public function getVersionCode () {

		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "SELECT version_code FROM $this->tablename WHERE id = 1");
        $this->throwExceptionOnError();


        mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_bind_result($stmt, $version_code);
		$this->throwExceptionOnError();

		mysqli_stmt_fetch($stmt);
		$this->throwExceptionOnError();


        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

		return $version_code;
    }

    /**
	 * Utility function to throw an exception if an error occurs
	 * while running a mysql command.
	 */
	private function throwExceptionOnError($link = null) {
		if($link == null) {
			$link = $this->conn;
		}
		if(mysqli_error($link)) {
			$msg = mysqli_errno($link) . ": " . mysqli_error($link);
			echo "$msg";
			throw new Exception('MySQL Error - '. $msg);
		}
	}

}

?>
