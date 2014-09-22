<?php


class kametiMemberService {

	var $username;
	var $password;
	var $server;
	var $port;
	var $databasename;
	var $tablename = "kameti_member";

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
	public function createKametiMember($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "INSERT INTO $this->tablename (kameti_id,member_id,user_name,can_admin_bid,can_admin_update_mobile,
							   can_admin_update_name,can_admin_update_pic) VALUES (?,?,?,?,?,?,?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'iisiiii', $item->kameti_id,$item->member_id,$item->user_name,$item->can_admin_bid,$item->can_admin_update_mobile,
							   $item->can_admin_update_name,$item->can_admin_update_pic);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$item->kameti_member_id = mysqli_stmt_insert_id($stmt);

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg =  "kameti member $item->user_name added sucessfully";

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
	public function updateKametiMember($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "UPDATE $this->tablename SET user_name=?, can_admin_bid=?, can_admin_update_mobile=?,
							   can_admin_update_name=?,can_admin_update_pic=? WHERE (kameti_id=? and member_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'siiiiii', $item->user_name, $item->can_admin_bid, $item->can_admin_update_mobile,
							   $item->can_admin_update_name,$item->can_admin_update_pic,$item->kameti_id, $item->member_id);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg	= "updateKametiMember";

		return $item;
	}

	/**
	 * Returns all the rows from the table.
	 *
	 * Add authroization or any logical checks for secure access to your data
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getKametiMember($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT kameti_member_id, user_name, can_admin_bid, can_admin_update_mobile,
							   can_admin_update_name,can_admin_update_pic FROM $this->tablename WHERE (kameti_id=? and member_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'ii', $item->kameti_id,$item->member_id);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_bind_result($stmt, $item->kameti_member_id, $item->user_name, $item->can_admin_bid, $item->can_admin_update_mobile,
							   $item->can_admin_update_name,$item->can_admin_update_pic);

		mysqli_stmt_fetch($stmt);
		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "get Kameti Member" . $item->kameti_id . $item->member_id;
	    return $item;
	}

	/**
	 * Returns all the rows from the table.
	 *
	 * Add authroization or any logical checks for secure access to your data
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getAllKametiMember($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT kameti_member_id, member_id, user_name, can_admin_bid, can_admin_update_mobile,
							   can_admin_update_name,can_admin_update_pic FROM $this->tablename WHERE kameti_id=?");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'i', $item->kameti_id);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$rows = array();

		mysqli_stmt_bind_result($stmt, $row->kameti_member_id, $row->member_id, $row->user_name, $row->can_admin_bid, $row->can_admin_update_mobile,
							   $row->can_admin_update_name,$row->can_admin_update_pic);

		while (mysqli_stmt_fetch($stmt)) {
	      $rows[] = $row;
	      $row = new stdClass();
	      mysqli_stmt_bind_result($stmt, $row->kameti_member_id, $row->member_id, $row->user_name, $row->can_admin_bid, $row->can_admin_update_mobile,
							   $row->can_admin_update_name,$row->can_admin_update_pic);
	    }

		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);

		$item->getAllKametiMember = $rows;
		$item->result = 1;

		$item->msg = "Get All Kameti Member" . $item->kameti_id;

	    return $item;
	}

	/**
	 * Returns getKametiMemberID.
	 *
	 * Add authroization or any logical checks for secure access to your data
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getKametiMemberID($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT kameti_member_id FROM $this->tablename WHERE (kameti_id=? and member_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'ii', $item->kameti_id,$item->member_id);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();


		mysqli_stmt_bind_result($stmt, $item->kameti_member_id);

		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "Got Kameti Member ID";

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
	public function deleateKametiMember ($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "DELETE FROM $this->tablename WHERE (kameti_id=? and member_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'ii', $item->kameti_id,$item->member_id);
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "Deleted" . $item->kameti_id . $item->member_id;

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
	public function deleateAllKametiMember ($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		
		$stmt = mysqli_prepare($this->connection, "DELETE FROM $this->tablename WHERE kameti_id=?");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'i', $item->kameti_id);
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "Deleted" . $item->kameti_id;

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
			echo "$msg";
			throw new Exception('MySQL Error - '. $msg);
		}
	}
}

?>
