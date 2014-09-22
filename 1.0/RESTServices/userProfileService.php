<?php


class userProfileService {

	var $username;
	var $password;
	var $server;
	var $port;
	var $databasename;
	var $tablename = "members";

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

		$this->throwExceptionOnError($this->connection);
	}

	/**
	 * Returns the item corresponding to the value specified for the primary key.
	 *
	 * Add authorization or any logical checks for secure access to your data 
	 *
	 * 
	 * @return stdClass
	 */
	public function createUserProfile($item) {

		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);


		$stmt = mysqli_prepare($this->connection, "INSERT INTO $this->tablename (mobile_number) VALUES (?)");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 's', $item->mobile_number);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);		
		$this->throwExceptionOnError();

		$item->member_id = mysqli_stmt_insert_id($stmt);
		
		mysqli_stmt_free_result($stmt);		
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg =  "User $item->mobile_number created sucessfully";
		
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
	public function updateUserProfile($item) {

		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);

		$stmt = mysqli_prepare($this->connection, "UPDATE $this->tablename SET mobile_number?, user_name=?, pic=? WHERE mobile_number=?");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'ssbs', $item->mobile_number, $item->user_name, $item->pic, $item->mobile_number);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);		
		$this->throwExceptionOnError();
		
		mysqli_stmt_free_result($stmt);		
		mysqli_close($this->connection);
	}
	
	/**
	 * Returns all the rows from the table.
	 *
	 * Add authroization or any logical checks for secure access to your data 
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getUserProfile($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);

		$stmt = mysqli_prepare($this->connection, "SELECT member_id, user_name, pic FROM $this->tablename WHERE mobile_number = ?");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 's', $item->mobile_number);
		$this->throwExceptionOnError();
		
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_result($stmt, $item->member_id, $item->user_name, $item->pic);
		
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);
		
	    return $item;
	}

	/**
	 * Returns member_id.
	 *
	 * Add authroization or any logical checks for secure access to your data 
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getUserID($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT member_id FROM $this->tablename WHERE mobile_number = ?");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 's', $item->mobile_number);
		$this->throwExceptionOnError();
		
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();
		

		mysqli_stmt_bind_result($stmt, $item->member_id);
		
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);
		
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
	public function deleateUserProfile ($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);

		$stmt = mysqli_prepare($this->connection, "DELETE FROM $this->tablename WHERE mobile_number = ?");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 'i', $item->mobile_number);
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();
		
		mysqli_stmt_free_result($stmt);		
		mysqli_close($this->connection);
		
		$item->result = 1;
		$item->msg = "user for" . $item->mobile_number . "is deleted";

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
