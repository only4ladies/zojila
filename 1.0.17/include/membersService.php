<?php

/**
 * Class to handle all kameti_users db operations
 * This class will have CRUD methods for database tables
 *
 * @author Pramod Kumar Raghav
 *
 */

class membersService {

    private $conn;
	private $dbConnect;
	private $tablename;

    function __construct() {
        require_once dirname(__FILE__) . '/dbConnect.php';
        // opening db connection
        $this->dbConnect = new dbConnect();
		$this->tablename = "kameti_members";
    }

    /* ------------- `kameti_users` table method ------------------ */

    /**
     * Creating new Kameti User
     * method POST
     * @param String Admin $member_id user id to whom kameti belongs to or kameti admin
     * @param String $kameti_member_name name of the kameti user
     * @param Int $kameti_user_mobile Mobile number of kameti user
     * @return bool true/false
     */
    public function createMember($item) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "INSERT INTO $this->tablename (kameti_id, member_id, member_name) VALUES (?,?,?)");
		$this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, 'iis', $item->kameti_id, $item->member_id, $item->member_name);
		$this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$member_id = mysqli_stmt_insert_id($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

        if ($result) {
            return $member_id;
        } else {
            // kameti failed to create
            return NULL;
        }
    }

	/**
     * Delete the kameti Member
     * method DELETE
     * @return bool true/false
     */
    public function deleteMember($kameti_id, $member_id) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "DELETE FROM $this->tablename WHERE ( kameti_id = ? AND member_id = ? )");
		$this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, 'ii', $kameti_id, $member_id);
		$this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

        if ($result) {
            return TRUE;
        } else {
            // kameti failed to create
            return FALSE;
        }
    }

	/**
     * Checking for isMemberExists for the given kameti_id
     * @param String $kameti_id, $member_id
     * @return boolean
     */
    public function isMemberExists($kameti_id, $member_id) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();
        $stmt = mysqli_prepare($this->conn,"SELECT COUNT(*) AS COUNT FROM $this->tablename  WHERE (kameti_id = ? AND member_id=?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, "ii", $kameti_id, $member_id);
        $this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_bind_result($stmt, $count);
		$this->throwExceptionOnError();

		mysqli_stmt_fetch($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
		mysqli_close($this->conn);

		// Check for successful count
        if ($count > 0) {
            return TRUE;
        }else{
            return FALSE;
        }
    }


	/**
     * get the member detail by member_id for the given kameti_id
     * @param String $kameti_id, $member_id
     * @return boolean
     */
    public function getMemberByID($id) {
	
		$query = "SELECT id, kameti_id, member_id, member_name, can_admin_bid, can_admin_update_mobile, can_admin_update_name,
							   can_admin_update_email,can_admin_update_pic FROM $this->tablename  WHERE id = $id)";
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();
        $stmt = mysqli_prepare($this->conn, "SELECT id, kameti_id, member_id, member_name, can_admin_bid, can_admin_update_mobile, can_admin_update_name,
							   can_admin_update_email,can_admin_update_pic FROM $this->tablename  WHERE id = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, "i", $id);
        $this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_bind_result($stmt, $item->id, $item->kameti_id, $item->member_id, $item->member_name, $item->can_admin_bid, $item->can_admin_update_mobile, $item->can_admin_update_name, $item->can_admin_update_email, $item->can_admin_update_pic);
		$this->throwExceptionOnError();

		mysqli_stmt_store_result($stmt);
        $this->throwExceptionOnError();

		mysqli_stmt_fetch($stmt);
		$this->throwExceptionOnError();

		$rows = mysqli_stmt_num_rows($stmt);
		mysqli_stmt_free_result($stmt);
		mysqli_close($this->conn);

		// Check for successful count
        if ($rows > 0) {
            return $item;
        }else{
            return $query;
        }
    }
	
	
	/* Get all the users for the given kameti if member_id is the member of the given kameti_id
	 * method GET
	 * @param none
	 * retuen A list of kameties for the given member_id
	 */

	public function getAllMembers($kameti_id){
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

		$stmt = mysqli_prepare($this->conn, "SELECT id, kameti_id, member_id, member_name, can_admin_bid, can_admin_update_mobile,
                               can_admin_update_name, can_admin_update_email, can_admin_update_pic FROM $this->tablename WHERE kameti_id = ?");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'i', $kameti_id);
		$this->throwExceptionOnError();


		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$rows = array();

		mysqli_stmt_bind_result($stmt, $item->id, $item->kameti_id, $item->member_id, $item->member_name, $item->can_admin_bid, $item->can_admin_update_mobile,
                               $item->can_admin_update_name, $item->can_admin_update_email, $item->can_admin_update_pic);
		$this->throwExceptionOnError();

		while (mysqli_stmt_fetch($stmt)) {
			$rows[] = $item;
			$item = new stdClass();
			mysqli_stmt_bind_result($stmt, $item->id, $item->kameti_id, $item->member_id, $item->member_name, $item->can_admin_bid, $item->can_admin_update_mobile,
                               $item->can_admin_update_name, $item->can_admin_update_email,  $item->can_admin_update_pic);

		}
		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->conn);

		if(count($rows) > 0){
			return $rows;
		}else{
			return NULL;
		}

	}


	public function getKametiAllMembers($kametiList){
		$query = "SELECT DISTINCT member_id FROM $this->tablename WHERE ";

		for ($k=0; $k<=count($kametiList) - 1; $k++) {
			//TODO
            $kametiObj = $kametiList[$k];

			$kameti_id = $kametiObj->id;
			if($k == count($kametiList) - 1){
				$query = $query . "kameti_id = $kameti_id ";
			}else{
				$query = $query . "kameti_id = $kameti_id or ";
			}
		}


		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

		$stmt = mysqli_prepare($this->conn, $query);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$rows = array();

		mysqli_stmt_bind_result($stmt, $item->member_id);
		$this->throwExceptionOnError();

		while (mysqli_stmt_fetch($stmt)) {
			$rows[] = $item;
			$item = new stdClass();
			mysqli_stmt_bind_result($stmt, $item->member_id);

		}
		mysqli_stmt_free_result($stmt);
	    mysqli_close($this->conn);

		if(count($rows) > 0){
			return $rows;
		}else{
			return NULL;
		}

	}


	/**
     * Updating user info
     * @param String $mobile User mobile
     */
    public function updateUserName($item) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET member_name = ? WHERE (kameti_id = ? AND member_id = ?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"sii", $item->name, $item->kameti_id, $item->member_id);
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
     * Updating can_admin_update_name info
     * @param String $item User mobile
     */
    public function changeUserNamePermission($item) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET can_admin_update_name = ? WHERE (kameti_id = ? AND member_id = ?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"si", $item->can_admin_update_name, $item->kameti_id, $item->member_id);
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
     * Updating can_admin_update_name info
     * @param String $item User mobile
     */
    public function changeUserMobilePermission($item) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET can_admin_update_mobile= ? WHERE (kameti_id = ? AND member_id = ?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"si", $item->can_admin_update_mobile, $item->kameti_id, $item->member_id);
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
     * Updating can_admin_update_name info
     * @param String $item User mobile
     */
    public function changeUserEmailPermission($item) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET can_admin_update_email = ? WHERE (kameti_id = ? AND member_id = ?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"si", $item->can_admin_update_email, $item->kameti_id, $item->member_id);
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
     * Updating can_admin_update_name info
     * @param String $item User mobile
     */
    public function changeUserPicPermission($item) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET can_admin_update_pic = ? WHERE (kameti_id = ? AND member_id = ?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"si", $item->can_admin_update_pic, $item->kameti_id, $item->member_id);
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
     * Updating can_admin_update_name info
     * @param String $item User mobile
     */
    public function changeUserBidPermission($item) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET can_admin_update_bid = ? WHERE (kameti_id = ? AND member_id = ?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"si", $item->can_admin_update_bid, $item->kameti_id, $item->member_id);
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