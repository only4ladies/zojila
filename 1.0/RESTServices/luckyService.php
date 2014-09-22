<?php


class luckyService {

	var $username;
	var $password;
	var $server;
	var $port;
	var $databasename;
	var $tablename = "lucky";

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
	public function createLucky($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "INSERT INTO $this->tablename (kameti_id,member_id,auction_id,amount,winner_type) VALUES (?,?,?,?,?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'iiiis', $item->kameti_id,$item->member_id,$item->auction_id,$item->amount,$item->winner_type);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$item->kameti_member_id = mysqli_stmt_insert_id($stmt);

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg =  "createLucky";

		return $item;
	}

	/**
	 * Updates the passed item in the table.
	 *
	 * Add authorization or any logical checks for secure access to your data
	 *
	 * @param stdClass $item
	 * @return void
	 */
	public function updateLucky($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "UPDATE $this->tablename SET amount=?,winner_type=? WHERE (kameti_id=? AND member_id=? AND auction_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'isiii', $item->amount, $item->winner_type, $item->kameti_id, $item->member_id, $item->auction_id);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg	= "updateLucky";

		return $item;
	}

	/**
	 * Returns all the rows from the table.
	 *
	 * Add authroization or any logical checks for secure access to your data
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getLucky($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT amount=?,winner_type=? FROM $this->tablename WHERE WHERE (kameti_id=? AND member_id=? AND auction_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'isiii', $item->amount,$item->winner_type,$item->kameti_id,$item->member_id,$item->auction_id);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_bind_result($stmt, $item->amount,$item->winner_type);

		mysqli_stmt_fetch($stmt);
		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "getLucky";

	    return $item;
	}

	/**
	 * Returns all the rows from the table.
	 *
	 * Add authroization or any logical checks for secure access to your data
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getAllLucky($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT member_id=?,amount=?,winner_type=? FROM $this->tablename WHERE (kameti_id=? AND auction_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'iisii', $item->member_id,$item->amount,$item->winner_type,$item->kameti_id,$item->auction_id);
		$this->throwExceptionOnError();



		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$rows = array();

		mysqli_stmt_bind_result($stmt, $row->member_id, $row->amount, $row->winner_type, $row->kameti_id, $row->auction_id);

		while (mysqli_stmt_fetch($stmt)) {
	      $rows[] = $row;
	      $row = new stdClass();
	      mysqli_stmt_bind_result($stmt, $row->member_id, $row->amount, $row->winner_type, $row->kameti_id, $row->auction_id);
	    }

		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);

		$item->getAllLucky = $rows;
		$item->result = 1;

		$item->msg = "getAllLucky";

	    return $item;
	}

	/**
	 * Returns getKametiMemberID.
	 *
	 * Add authroization or any logical checks for secure access to your data
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getLuckyID($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT lucky_id FROM $this->tablename WHERE (kameti_id=? AND member_id=? AND auction_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'iii', $item->kameti_id,$item->member_id,$item->auction_id);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();


		mysqli_stmt_bind_result($stmt, $item->lucky_id);

		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "getLuckyID";

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
	public function deleateLucky ($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "DELETE FROM $this->tablename WHERE (kameti_id=? AND member_id=? AND auction_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'iii', $item->kameti_id,$item->member_id,$item->auction_id);
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "deleateLucky";

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
	public function deleateAllLucky ($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "DELETE FROM $this->tablename WHERE (kameti_id=? AND member_id=?");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'ii', $item->kameti_id,$item->member_id);
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "deleateAllLucky";

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
