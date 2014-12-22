<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Pramod Kumar Raghav
 *
 */
class userService {

    private $conn;
	private $dbConnect;
	private $tablename;

    function __construct() {
        require_once dirname(__FILE__) . '/dbConnect.php';
		require_once dirname(__FILE__) . '/passHash.php';


        // opening db connection
        $this->dbConnect = new dbConnect();
		$this->tablename = "users";
    }

	/**
     * Checking user login
     * @param String $mobile User login id
     * @param String $device_key
     * @param String $password user password
     * @return boolean User login status success/fail
     */
    public function checkLogin($item) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        // fetching user id from users by mobile number
        $stmt = mysqli_prepare($this->conn, "SELECT d.user_id FROM device d , users u WHERE d.device_key=? AND d.user_id = u.id AND u.mobile = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, "ss", $item->device_key, $item->mobile);
        $this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_bind_result($stmt, $user_id);
        $this->throwExceptionOnError();

        mysqli_stmt_store_result($stmt);
        $this->throwExceptionOnError();

		mysqli_stmt_fetch($stmt);
		$this->throwExceptionOnError();

        $rows = mysqli_stmt_num_rows($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);


        if ($rows > 0) {
			return $user_id;
        } else {
            // user not existed with the mobile
            return NULL;
        }
    }



    /* ------------- `users` table method ------------------ */

    /**
     * Creating new user
     * @param String $name User full name
     * @param String $mobile User mobile number
     * @param String $device_key User Mobile unique ID
     * @param String $device_name User Mobile name
     * @param String $email User email id
     * @param Blob $pic User profile pic
     * @param String $password User 4 digit password key
     */
    public function createUser($item) {
        require_once 'passHash.php';
		
        // First check if user already existed in db
        if (!$this->isUserExists($item->mobile)) {

			if(!isset($item->password)){
				$item->password = "";
			}
            // Generating password hash
            $item->password_hash = passHash::hash($item->password);

			// Get the DB Connection
			$this->conn = $this->dbConnect->connect();

            // insert query
            $stmt = mysqli_prepare($this->conn, "INSERT INTO $this->tablename (name, mobile, email, password_hash, developer_payload,created_at,trial_period) values(?,?, ?, ?, ?, ?,?)");
            $this->throwExceptionOnError();

            mysqli_stmt_bind_param($stmt,"ssssssi", $item->name, $item->mobile, $item->email, $item->password_hash, $item->developer_payload, $item->created_at, $item->trial_period);
            $this->throwExceptionOnError();

            $result = mysqli_stmt_execute($stmt);
            $this->throwExceptionOnError();

            $item->user_id = mysqli_stmt_insert_id($stmt);

            mysqli_stmt_free_result($stmt);
            mysqli_close($this->conn);

            // Check for successful insertion
            if ($result) {
                // User successfully inserted
				return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return USER_CREATE_FAILED;
            }
        } else {
            // User with same mobil already existed in the db
            return USER_ALREADY_EXISTED;
        }
    }

    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    public function isUserExists($mobile) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();
        $stmt = mysqli_prepare($this->conn,"SELECT id from $this->tablename  WHERE mobile = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, "s", $mobile);
        $this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_store_result($stmt);
        $this->throwExceptionOnError();


        $rows = mysqli_stmt_num_rows($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

        return $rows > 0;
    }


    /**
     * Updating user info
     * @param String $name User full name
     */
    public function updateUserName($item) {

		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET name = ? WHERE id = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"si", $item->name, $item->member_id);
        $this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

		if($result){
			return true;
		}else{
			return false;
		}
    }

	/**
     * Updating user info
     * @param String $mobile User mobile
     */
    public function updateUserMobile($item) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET mobile=? WHERE id = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"si", $item->mobile, $item->member_id);
        $this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

		if($result){
			return true;
		}else{
			return false;
		}
    }


	/**
     * Updating user info
     * @param String $mobile User mobile
     */
    public function updateUserEmail($item) {

		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET email = ?  WHERE id = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"si", $item->email, $item->member_id);
        $this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

		if($result){
			return true;
		}else{
			return false;
		}
    }

	/**
     * Updating Developer Payload
     * @param String $id
     */
    public function updateDeveloperPayload($item) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET developer_payload = ? WHERE id = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"si", $item->developer_payload, $item->member_id);
        $this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

		if($result){
			return true;
		}else{
			return false;
		}
    }

	/**
     * Updating CreatedAT
     * @param String $id
     */
    public function updateCreatedAT($item) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET created_at = ? WHERE id = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"si", $item->created_at, $item->member_id);
        $this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

		if($result){
			return true;
		}else{
			return false;
		}
    }

	/**
     * Updating Trial Period
     * @param String $id
     */
    public function updateTrialPeriod($item) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET trial_period = ? WHERE id = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"si", $item->trial_period, $item->member_id);
        $this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

		if($result){
			return true;
		}else{
			return false;
		}
    }

    /**
     * Fetching user by mobile
     * @param String $mobile User mobile number
     * @return $user hash
     */
    public function getUserByMobile($mobile) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "SELECT id, name, mobile, email, password_hash ,developer_payload, status, created_at, trial_period FROM $this->tablename  WHERE mobile = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, "s", $mobile);
        $this->throwExceptionOnError();

		$result = mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_bind_result($stmt, $item->id, $item->name, $item->mobile, $item->email,  $item->password_hash, $item->developer_payload, $item->status, $item->created_at, $item->trial_period);
        $this->throwExceptionOnError();

        mysqli_stmt_store_result($stmt);
        $this->throwExceptionOnError();

		mysqli_stmt_fetch($stmt);
		$this->throwExceptionOnError();

        $rows = mysqli_stmt_num_rows($stmt);
        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

		if($rows > 0){
			return $item;
		}else{
			return NULL;
		}
    }

	/**
     * Fetching user by user_id
     * @param String $user_id User
     * @return $user hash
     */
    public function getUserByID($user_id) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "SELECT id, name, mobile, email, password_hash ,developer_payload, status, created_at, trial_period FROM $this->tablename  WHERE id = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, "i", $user_id);
        $this->throwExceptionOnError();

		$result = mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_bind_result($stmt, $item->id, $item->name, $item->mobile, $item->email,  $item->password_hash, $item->developer_payload, $item->status, $item->created_at, $item->trial_period);
        $this->throwExceptionOnError();

        mysqli_stmt_store_result($stmt);
        $this->throwExceptionOnError();

		mysqli_stmt_fetch($stmt);
		$this->throwExceptionOnError();

        $rows = mysqli_stmt_num_rows($stmt);
        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

		if($rows > 0){
			return $item;
		}else{
			return NULL;
		}
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
