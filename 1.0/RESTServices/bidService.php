<?php


class bidService {

	var $username;
	var $password;
	var $server;
	var $port;
	var $databasename;
	var $tablename = "bid";

	var $connection;

	/**
	 * The constructor initializes the connection to database. Everytime a request is
	 * received by Zend AMF, an instance of the service class is created and then the
	 * requested method is invoked.
	 */
	public function __construct() {
		$config = getConfigInfo();
		$this->server = $config->databaseserver;
		$this->username = $config->databaseusername;
		$this->password = $config->databasepassword;
		$this->databasename = $config->databasename;
	}

	/**
	 * Returns the item corresponding to the value specified for the primary key.
	 *
	 * Add authorization or any logical checks for secure access to your data
	 *
	 *
	 * @return stdClass
	 */
	public function createBid($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "INSERT INTO $this->tablename (auction_id, kameti_id, member_id, bid_time, bid_amount, bid_status) VALUES (?,?,?,?,?,?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'iiisis', $item->auction_id, $item->kameti_id, $item->member_id, $item->bid_time, $item->bid_amount, $item->bid_status);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$item->kameti_member_id = mysqli_stmt_insert_id($stmt);

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg =  "createBid";

		return $item;
	}


	/**
	 * Returns all the rows from the table.
	 *
	 * Add authroization or any logical checks for secure access to your data
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getAllBid($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT bid_id, member_id, bid_time, bid_amount, bid_status FROM $this->tablename WHERE (kameti_id=? AND auction_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'ii', $item->kameti_id, $item->auction_id);
		$this->throwExceptionOnError();



		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$rows = array();

		mysqli_stmt_bind_result($stmt, $row->bid_id, $row->member_id, $row->bid_time, $row->bid_amount, $row->bid_status);

		while (mysqli_stmt_fetch($stmt)) {
	      $rows[] = $row;
	      $row = new stdClass();
	      mysqli_stmt_bind_result($stmt, $row->bid_id, $row->member_id, $row->bid_time, $row->bid_amount, $row->bid_status);
	    }

		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);

		$item->getAllBid = $rows;
		$item->result = 1;

		$item->msg = "getAllBid";

	    return $item;
	}

	/**
	 * Returns getBidID.
	 *
	 * Add authroization or any logical checks for secure access to your data
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getBidID($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT bid_id FROM $this->tablename WHERE (auction_id=? AND member_id=? AND kameti_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'iii', $item->auction_id, $item->member_id, $item->kameti_id);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();


		mysqli_stmt_bind_result($stmt, $item->bid_id);

		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "getBidID";

	    return $item;
	}

	/**
	 * Deletes the item corresponding to the passed primary key value from
	 * the table.
	 *
	 * Add authorization or any logical checks for secure access to your data
	 *
	 *
	 * @return void
	 */
	public function deleateBid ($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "DELETE FROM $this->tablename WHERE (auction_id=? AND kameti_id=? AND member_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'iii', $item->auction_id, $item->kameti_id, $item->member_id);
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "deleateBid";

		return $item;
	}



	/**
	 * Deletes the item corresponding to the passed primary key value from
	 * the table.
	 *
	 * Add authorization or any logical checks for secure access to your data
	 *
	 *
	 * @return void
	 */
	public function deleateAllBid ($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "DELETE FROM $this->tablename  WHERE (auction_id=? AND kameti_id=? )");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'ii', $item->auction_id, $item->kameti_id);
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "deleateAllBid";

		return $item;
	}

	####################

	/**
	 * Utility function to throw an exception if an error occurs
	 * while running a mysql command.
	 */
	private function throwExceptionOnError($link = null) {
		if($link == null) {
			$link = $this->connection;
		}
		if(mysqli_error($link)) {
			$msg = mysqli_errno($link) . ": " . mysqli_error($link);
			throw new Exception('MySQL Error - '. $msg);
		}
	}
}

?>
