<?php


class deviceService {

	var $username;
	var $password;
	var $server;
	var $port;
	var $databasename;
	var $tablename = "device";

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
	public function createDevice($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "INSERT INTO $this->tablename (member_id,device_code,device_name) VALUES (?,?,?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'iss', $item->member_id,$item->device_code,$item->device_name);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$item->kameti_member_id = mysqli_stmt_insert_id($stmt);

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg =  "createDevice";

		return $item;
	}


	/**
	 * Returns all the rows from the table.
	 *
	 * Add authroization or any logical checks for secure access to your data
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getAllDevice($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT device_code=?,device_name=? FROM $this->tablename WHERE (member_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'ssi', $item->device_code,$item->device_name,$item->member_id);
		$this->throwExceptionOnError();



		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$rows = array();

		mysqli_stmt_bind_result($stmt, $row->device_code, $row->device_name);

		while (mysqli_stmt_fetch($stmt)) {
	      $rows[] = $row;
	      $row = new stdClass();
	      mysqli_stmt_bind_result($stmt, $row->device_code, $row->device_name);
	    }

		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);

		$item->getAllDevice = $rows;
		$item->result = 1;

		$item->msg = "getAllDevice";

	    return $item;
	}

	/**
	 * Returns getDeviceID.
	 *
	 * Add authroization or any logical checks for secure access to your data
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getDeviceID($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT device_id FROM $this->tablename WHERE (device_code=? AND member_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'si', $item->device_code,$item->member_id);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();


		mysqli_stmt_bind_result($stmt, $item->device_id);

		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "getDeviceID";

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
	public function deleateDevice ($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "DELETE FROM $this->tablename WHERE (device_code=? AND member_id=?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'si', $item->device_code,$item->member_id);
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "deleateDevice";

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
	public function deleateAllDevice ($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		
		$stmt = mysqli_prepare($this->connection, "DELETE FROM $this->tablename WHERE member_id=?");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'i', $item->member_id);
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "deleateAllDevice";

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
