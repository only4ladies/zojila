<?php


class auctionService {

	var $username;
	var $password;
	var $server;
	var $port;
	var $databasename;
	var $tablename = "auction";

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
	public function createAuction($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "INSERT INTO $this->tablename (kameti_id, auction_date, auction_start_time, auction_end_time, auction_winner, auction_runnerup,
							   minimum_bid_amount, maximum_bid_amount, member_profit, intrest_rate) VALUES (?,?,?,?,?,?,?,?,?,?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'isssiiiidd', $item->kameti_id, $item->auction_date, $item->auction_start_time, $item->auction_end_time, $item->auction_winner, $item->auction_runnerup,
							   $item->minimum_bid_amount,$item->maximum_bid_amount,$item->member_profit,$item->intrest_rate);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$item->kameti_member_id = mysqli_stmt_insert_id($stmt);

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg =  "createAuction";

		return $item;
	}


	/**
	 * Returns all the rows from the table.
	 *
	 * Add authroization or any logical checks for secure access to your data
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getAllAuction($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT auction_date, auction_start_time, auction_end_time, auction_winner, auction_runnerup, minimum_bid_amount,
								   maximum_bid_amount, member_profit, intrest_rate FROM $this->tablename WHERE (kameti_id=? AND auction_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'ii', $item->kameti_id, $item->auction_id);
		$this->throwExceptionOnError();



		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$rows = array();

		mysqli_stmt_bind_result($stmt, $row->auction_date, $row->auction_start_time, $row->auction_end_time, $row->auction_winner, $row->auction_runnerup,  $row->minimum_bid_amount,
								$row->maximum_bid_amount, $row->member_profit,  $row->intrest_rate);

		while (mysqli_stmt_fetch($stmt)) {
	      $rows[] = $row;
	      $row = new stdClass();
	      mysqli_stmt_bind_result($stmt, $row->auction_date, $row->auction_start_time, $row->auction_end_time, $row->auction_winner, $row->auction_runnerup,  $row->minimum_bid_amount,
								$row->maximum_bid_amount, $row->member_profit,  $row->intrest_rate);
	    }

		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);

		$item->getAllAuction = $rows;
		$item->result = 1;

		$item->msg = "getAllAuction";

	    return $item;
	}

	/**
	 * Returns getAuctionID.
	 *
	 * Add authroization or any logical checks for secure access to your data
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getAuctionID($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		
		$stmt = mysqli_prepare($this->connection, "SELECT auction_id FROM $this->tablename WHERE ( kameti_id=? AND auction_date=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'is', $item->kameti_id, $item->auction_date);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();


		mysqli_stmt_bind_result($stmt, $item->auction_id);

		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "getAuctionID";

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
	public function deleateAuction ($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		
		$stmt = mysqli_prepare($this->connection, "DELETE FROM $this->tablename WHERE (auction_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'i', $item->auction_id);
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "deleateAuction";

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
	public function deleateAllAuction ($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "DELETE FROM $this->tablename  WHERE (kameti_id=? )");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'i',  $item->kameti_id);
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "deleateAllAuction";

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
