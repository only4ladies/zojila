<?php

/**
 * Class to handle Mobile verification
 * This class will have CRUD methods for database tables
 *
 * @author Pramod Kumar Raghav
 *
 */


class userVerifyService {

	private $conn;
	private $dbConnect;
	private $tablename;

	public function __construct() {
        require_once dirname(__FILE__) . '/dbConnect.php';
        // opening db connection
        $this->dbConnect = new dbConnect();
		$this->tablename = "temp_verification";
    }

	/**
	 * Returns the item corresponding to the value specified for the primary key.
	 *
	 * Add authorization or any logical checks for secure access to your data 
	 *
	 * 
	 * @return stdClass
	 */
	public function createUserVerification($item) {
		$this->conn = $this->dbConnect->connect();
		$this->throwExceptionOnError($this->conn);
		
		$item->code = rand(1000,9999);
		
		$stmt = mysqli_prepare($this->conn, "INSERT INTO $this->tablename (code, mobile) VALUES (?,?)");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 'is', $item->code, $item->mobile);
		$this->throwExceptionOnError();

		$result = mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();
		
		mysqli_stmt_free_result($stmt);		
		mysqli_close($this->conn);

		// Check for successful insertion
        if ($result) {
            return $item;
        }else{
            return NULL;
        }
	}

	/**
	 * Send the newly generated verification code to appropriate  (mobile or email) destination
	 *
	 */
	public function sendVerificationCode($item) {		
		// Replace with your username
		$user = "praghav";
		
		// Replace with your API KEY (We have sent API KEY on activation email, also available on panel)
		$apikey = "k6HTDE4mouTFIZL6pX7J"; 
		
		// Replace if you have your own Sender ID, else donot change
		$senderid  =  "KAMETI"; 
		
		// Replace with the destination mobile Number to which you want to send sms
		$mobile  =  $item->mobile;
		
		// Replace with your Message content
		$message   =  $item->message;
		
		// For Plain Text, use "txt" ; for Unicode symbols or regional Languages like hindi/tamil/kannada use "uni"
		$type   =  "txt";
		
		$message = urlencode($message);
		$sendsms = "http://smshorizon.co.in/api/sendsms.php?user=" . $user
				. "&apikey=" . $apikey
				. "&mobile=" . $mobile
				. "&message=" . $message
				. "&type=" . $type
				. "&senderid=". $senderid
				;

		$ch = curl_init($sendsms);
		
		
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$msgid = curl_exec($ch);
		
		sleep (1);
		
		$ch = curl_init("http://smshorizon.co.in/api/status.php?user=".$user."&apikey=".$apikey."&msgid=".$msgid);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		
		curl_close($ch);
		
		return TRUE;
	}
	
	/**
	 * Returns all the rows from the table.
	 *
	 * Add authroization or any logical checks for secure access to your data 
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function verifyUserCode($item) {
		$this->conn = $this->dbConnect->connect();
		$this->throwExceptionOnError($this->conn);
		
		$stmt = mysqli_prepare($this->conn, "SELECT COUNT(*) AS COUNT FROM $this->tablename WHERE (code = ? AND mobile = ? )");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 'is', $item->code, $item->mobile);
		$this->throwExceptionOnError();
		
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_result($stmt, $rec_count);
		$this->throwExceptionOnError();
		
		mysqli_stmt_fetch($stmt);
		$this->throwExceptionOnError();
		
		mysqli_stmt_free_result($stmt);
		mysqli_close($this->conn);
		
		if($rec_count > 0){
			return TRUE;
		}else{
			return FALSE;
		}
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
	public function deleateUserVerification($item) {
		$this->conn = $this->dbConnect->connect();
		$this->throwExceptionOnError($this->conn);
		
		$stmt = mysqli_prepare($this->conn, "DELETE FROM $this->tablename WHERE (mobile = ?)");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 's', $item->mobile);
		$this->throwExceptionOnError();

		$result = mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();
		
		mysqli_stmt_free_result($stmt);		
		mysqli_close($this->conn);

		if($result){
			return true;
		}else {
			return false;
		}
	}
	
	####################
	
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
			throw new Exception('MySQL Error - '. $msg);
		}		
	}
}

?>
