<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Pramod Kumar Raghav
 *
 */
class deviceService {

    private $conn;
	private $dbConnect;
	private $tablename;

    function __construct() {
        require_once dirname(__FILE__) . '/dbConnect.php';
        // opening db connection
        $this->dbConnect = new dbConnect();
		$this->tablename = "device";
    }


    /**
     * Insert new device key for the given user
     * @param int $user_id
     * @param String $device_key   : Mobile unique ID
     * @param String $device_name  : Mobile Name
     * @return Bool true/false
     */

    public function createDevice($item) {

		// let first remove other device for the same user
		$this->deleteDevice($item->user_id);

		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

		// insert query
        $stmt = mysqli_prepare($this->conn, "INSERT INTO $this->tablename (user_id, device_key, device_name) values(?, ?, ?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"iss", $item->user_id, $item->device_key, $item->device_name);
        $this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

        // Check for successful insertion
        if ($result) {
            return TRUE;
        }else{
            return FALSE;
        }
	}

    /**
     * Deleting the device key from the device table for the given user
     * @param int $user_id
     * @param String $device_key   : Mobile unique ID
     * @param String $device_name : Mobile Name
     * @return Bool true/false
     */

    public function deleteDevice($user_id) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

		// insert query
        $stmt = mysqli_prepare($this->conn, "DELETE FROM $this->tablename WHERE(user_id=?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"i", $user_id);
        $this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

        // Check for successful deletion
        if ($result) {
            return TRUE;
        }else{
            return FALSE;
        }
	}


    /**
     * Checking device_key for the given user id
     * @param String $user_id User login id
     * @param String $device_key
     * @return boolean User device_key status success/fail
     */
    public function isDeviceKeyExists($item) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

        // fetching device_key from device by user_id
        $stmt = mysqli_prepare($this->conn, "SELECT id FROM $this->tablename WHERE (user_id = ? AND device_key=?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, "is", $item->user_id, $item->device_key);
        $this->throwExceptionOnError();

        mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_store_result($stmt);
        $this->throwExceptionOnError();


        $rows = mysqli_stmt_num_rows($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

        if ($rows > 0) {
            // User device_key is correct
            return TRUE;
        }else{
            // user device_key is incorrect
            return FALSE;
        }
    }


    /**
     * Fetching user id by $device_key
     * @param String $device_key user device key
     * @return $user_id
     * TO DO : getUserId and isValidDeviceKey are same need to get logic to remove redendancey.
     */
    public function getUserIdByDeviceKey($device_key) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "SELECT user_id FROM $this->tablename WHERE device_key = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, "s", $device_key);
        $this->throwExceptionOnError();

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $user_id);
            $this->throwExceptionOnError();

            mysqli_stmt_fetch($stmt);
            $this->throwExceptionOnError();

            mysqli_stmt_free_result($stmt);
            mysqli_close($this->conn);
            // TODO
            // $user_id = $stmt->get_result()->fetch_assoc();
            return $user_id;
        } else {
            return NULL;
        }
    }

	/**
     * Fetching user id by $device_key
     * @param String $device_key user device key
     * @return $user_id
     * TO DO : getUserId and isValidDeviceKey are same need to get logic to remove redendancey.
     */
    public function getDeviceKeyByUserID($user_id) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "SELECT device_key FROM $this->tablename WHERE user_id = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, "s", $user_id);
        $this->throwExceptionOnError();

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $device_key);
            $this->throwExceptionOnError();

            mysqli_stmt_fetch($stmt);
            $this->throwExceptionOnError();

            mysqli_stmt_free_result($stmt);
            mysqli_close($this->conn);
            // TODO
            // $user_id = $stmt->get_result()->fetch_assoc();
            return $device_key;
        } else {
            return NULL;
        }
    }

    /**
     * Validating user device key
     * If the device key is there in db, it is a valid key
     * @param String $device_key user device key
     * @return boolean
     */
    public function isValidDeviceKey($device_key) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "SELECT id from $this->tablename WHERE device_key = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, "s", $device_key);
        $this->throwExceptionOnError();

        mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_store_result($stmt);
        $this->throwExceptionOnError();

        $rows = mysqli_stmt_num_rows($stmt);
        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);
        return $rows > 0;
    }

	/**
     * Fetching user device_key
     * @param String $user_id user id primary key in user table
     * @param String $device_key user id primary key in user table
     * @rturn Boolen True/False
     */
    public function getApiKeyById($user_id,$device_key) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "SELECT id FROM $this->tablename WHERE (user_id = ? AND device_key=?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, "is", $user_id, $device_key);
        $this->throwExceptionOnError();

        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_store_result($stmt);
            $this->throwExceptionOnError();


            $rows = mysqli_stmt_num_rows($stmt);
            $this->throwExceptionOnError();

            mysqli_stmt_free_result($stmt);
            mysqli_close($this->conn);

            if($rows > 0){
               return TRUE;
            }else{
                return FALSE;
            }
        } else {
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
