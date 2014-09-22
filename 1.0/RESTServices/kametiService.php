<?php



class kametiService {

	var $username;
	var $password;
	var $server;
	var $port;
	var $databasename;
	var $tablename = "kameti";

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
	public function createKameti($item) {
		
		/**
		 * Let first get the member id from the member table to store this as admin in kameti
		 *  We might to change
		 *  $item->kameti_start_date->toString('YYYY-MM-dd')
		 *  $item->bid_end_time->toString('HH:mm:ss')
		 *
		 */
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "INSERT INTO $this->tablename (kameti_name,admin_id,kameti_start_date,kameti_members,kameti_amount,kameti_intrest_rate,
							   bid_start_time,bid_end_time,bid_amout_minimum,bid_timer,lucky_draw_amount,lucky_members,runnerup_percentage,kameti_rule) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 'sisiidssiiiiii', $item->kameti_name,$item->member_id,$item->kameti_start_date,$item->kameti_members,$item->kameti_amount,$item->kameti_intrest_rate,$item->bid_start_time,
							   $item->bid_end_time,$item->bid_amout_minimum,$item->bid_timer,$item->lucky_draw_amount,$item->lucky_members,
							   $item->runnerup_percentage,$item->kameti_rule);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);		
		$this->throwExceptionOnError();
		
		$item->kameti_id = mysqli_stmt_insert_id($stmt);
		mysqli_stmt_free_result($stmt);		
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg	= "Kameti for" . $item->mobile_number . "created sucessfully";
		
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
	public function updateKameti($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "UPDATE $this->tablename SET kameti_name=?, admin_id=?, kameti_start_date=?, kameti_members=?, kameti_amount=?,
							   kameti_intrest_rate=?, bid_start_time=?, bid_end_time=?, bid_amout_minimum=?, bid_timer=?, lucky_draw_amount=?, lucky_members=?,
							   runnerup_percentage=?, kameti_rule=? WHERE kameti_id=?");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 'sisiidssiiiii', $item->kameti_name, $item->admin_id, $item->kameti_start_date, $item->kameti_members, $item->kameti_amount,
							   $item->kameti_intrest_rate, $item->bid_start_time, $item->bid_end_time, $item->bid_amout_minimum, $item->bid_timer, $item->lucky_draw_amount,
							   $item->lucky_members, $item->runnerup_percentage, $item->kameti_rule, $item->kameti_id);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);		
		$this->throwExceptionOnError();
		
		mysqli_stmt_free_result($stmt);		
		mysqli_close($this->connection);

		$item->result = 1;
		$item->msg = "updateKameti";

		return $item;
	}
	
	/**
	 * Returns all the rows from the table.
	 *
	 * Add authroization or any logical checks for secure access to your data 
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getKameti($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT kameti_name, admin_id, kameti_start_date, kameti_members, kameti_amount, kameti_intrest_rate,
							   bid_start_time, bid_end_time, bid_amout_minimum, bid_timer, lucky_draw_amount, lucky_members, runnerup_percentage, kameti_rule
							   FROM $this->tablename WHERE kameti_id = ?");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 's', $item->kameti_id);
		$this->throwExceptionOnError();
		
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();
		
		$rows = array();
		mysqli_stmt_bind_result($stmt, $row->kameti_name, $row->admin_id, $row->kameti_start_date, $row->kameti_members, $row->kameti_amount, $row->kameti_intrest_rate,
								$row->bid_start_time, $row->bid_end_time, $row->bid_amout_minimum, $row->bid_timer, $row->lucky_draw_amount, $row->lucky_members, $row->runnerup_percentage,
								$row->kameti_rule);
		
		while (mysqli_stmt_fetch($stmt)) {
			$rows[] = $row;
			$row = new stdClass();
			mysqli_stmt_bind_result($stmt, $row->kameti_name, $row->admin_id, $row->kameti_start_date, $row->kameti_members, $row->kameti_amount, $row->kameti_intrest_rate,
								$row->bid_start_time, $row->bid_end_time, $row->bid_amout_minimum, $row->bid_timer, $row->lucky_draw_amount, $row->lucky_members, $row->runnerup_percentage,
								$row->kameti_rule);
		}
		
		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->connection);
		
		$item->getKameti = $rows;
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
	public function deleateKameti ($item) {
		$this->connection = mysqli_connect(	$this->server,	$this->username, $this->password, $this->databasename );
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "DELETE FROM $this->tablename WHERE kameti_id = ?");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 'i', $item->kameti_id);
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();
		
		mysqli_stmt_free_result($stmt);		
		mysqli_close($this->connection);
		
		$item->result = 1;
		$item->msg = "deleateKameti";
		
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
