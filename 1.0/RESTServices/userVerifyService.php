<?php


class userVerifyService {

	var $username;
	var $password;
	var $server;
	var $port;
	var $databasename;
	var $tablename = "temp_verification";

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
	public function createUserVerification($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$item->verification_code = rand(1000,9999);
		
		$stmt = mysqli_prepare($this->connection, "INSERT INTO $this->tablename (verification_code, mobile_number) VALUES (?,?)");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 'is', $item->verification_code, $item->mobile_number);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);		
		$this->throwExceptionOnError();
		
		mysqli_stmt_free_result($stmt);		
		mysqli_close($this->connection);

		return $item;
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
		$mobile  =  $item->mobile_number; 
		
		// Replace with your Message content
		$message   =  "Your mobile verification code for KAMETI is: " . $item->verification_code; 
		$message = urlencode($message);
		
		// For Plain Text, use "txt" ; for Unicode symbols or regional Languages like hindi/tamil/kannada use "uni"
		$type   =  "txt";
		
		
		#$sms_url = "http://smshorizon.co.in/api/sendsms.php?user=".$user."&apikey=".$apikey."&mobile=".$mobile."&senderid=".$senderid."&message=".$message."&type=".$type."";
		
		$ch = curl_init("http://smshorizon.co.in/api/sendsms.php?user=".$user."&apikey=".$apikey."&mobile=".$mobile."&senderid=".$senderid."&message=".$message."&type=".$type."");
		
		
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$msgid = curl_exec($ch);
		
		sleep (1);
		
		$ch = curl_init("http://smshorizon.co.in/api/status.php?user=".$user."&apikey=".$apikey."&msgid=".$msgid);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		
		curl_close($ch);
		
		$item->result = 1;
		$item->msg = "msg";
		
		return $item;
	}
	
	/**
	 * Returns all the rows from the table.
	 *
	 * Add authroization or any logical checks for secure access to your data 
	 *
	 * @return arraySELECT COUNT(*) AS COUNT FROM $this->tablename
	 */
	public function getUserVerification($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "SELECT COUNT(*) AS COUNT FROM $this->tablename WHERE (verification_code = ? AND mobile_number = ? )");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 'is', $item->verification_code, $item->mobile_number);
		$this->throwExceptionOnError();
		
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_result($stmt, $rec_count);
		$this->throwExceptionOnError();
		
		mysqli_stmt_fetch($stmt);
		$this->throwExceptionOnError();
		
		mysqli_stmt_free_result($stmt);
		mysqli_close($this->connection);
		
		if($rec_count){
			$item->result = 1;
			$item->msg = "Verification is done for $item->mobile_number!";
		}else{
			$item->result = 0;
			$item->msg = "Eighter Code or Mobile number is wrong!";
		}

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
	public function deleateUserVerification($item) {
		$this->connection = mysqli_connect($this->server,$this->username,$this->password,$this->databasename);
		$this->throwExceptionOnError($this->connection);
		
		$stmt = mysqli_prepare($this->connection, "DELETE FROM $this->tablename WHERE (mobile_number = ? and verification_code = ?)");
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 'is', $item->verification_code, $item->mobile_number);
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();
		
		mysqli_stmt_free_result($stmt);		
		mysqli_close($this->connection);

		$item->resul = 1;
		$item->msg = "deleateUserVerification";

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
