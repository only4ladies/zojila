<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Pramod Kumar Raghav
 *
 */
class inappProductService {

    private $conn;
	private $dbConnect;
	private $tablename;

    function __construct() {
        require_once dirname(__FILE__) . '/dbConnect.php';
        // opening db connection
        $this->dbConnect = new dbConnect();
		$this->tablename = "inapp_product";
    }



	public function getInAppProducts(){
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

		$stmt = mysqli_prepare($this->conn, "SELECT product_id, subscription_period, description FROM $this->tablename");
		$this->throwExceptionOnError();


		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$rows = array();

        mysqli_stmt_bind_result($stmt, $item->product_id, $item->subscription_period, $item->description);
		$this->throwExceptionOnError();

		while (mysqli_stmt_fetch($stmt)) {
			$rows[] = $item;
			$item = new stdClass();
			mysqli_stmt_bind_result($stmt, $item->product_id, $item->subscription_period, $item->description);

		}
		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->conn);

		return $rows;
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
