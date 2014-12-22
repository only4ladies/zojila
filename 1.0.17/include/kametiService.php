<?php

/**
 * Class to handle all kameti db operations
 * This class will have CRUD methods for database tables
 *
 * @author Pramod Kumar Raghav
 *
 */

class kametiService {

    private $conn;
	private $dbConnect;
	private $tablename;

    function __construct() {
        require_once dirname(__FILE__) . '/dbConnect.php';
        // opening db connection
        $this->dbConnect = new dbConnect();
		$this->tablename = "kameti";
    }

    /* ------------- `kameti` table method ------------------ */

    /**
     * Creating new Kameti
     * method POST
     * @param String $user_id user id to whom kameti belongs to or kameti admin
     * @param String $kameti_name name of the Kameti
     * @param Int $admin_id to whom kameti belongs to or kameti admin
     * @param Date $kameti_start_date Kameti start date
     * @param Int $kameti_members number of kameti members
     * @param Int $kameti_amount total kameti amount
     * @param Flote $kameti_interest_rate Rate of intrest for the given kameti
     * @param Time $bid_start_time It the bid time for kameti auction
     * @param Time $bid_end_time when the bidding will finished
     * @param Int $bid_amount_minimum
     * @param Int $bid_timer
     * @param Int $lucky_draw_amount
     * @param Int $lucky_members
     * @param Int $runnerup_percentage
     * @param Int $kameti_rule
     * @param Array $members_list ("members_list":[{9891533910:"PKR"},{9891533911:"XYZ"}])
     * @return bool id on success and false on failure
     */
    public function createKameti($item) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "INSERT INTO $this->tablename (kameti_name, admin_id, kameti_start_date, kameti_members, kameti_amount,
                               kameti_interest_rate, bid_start_time, bid_end_time, bid_amount_minimum,bid_timer, lucky_draw_amount, lucky_members,
                               runnerup_percentage, kameti_rule, kameti_status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, 'sisiidssiiiiiis', $item->kameti_name, $item->admin_id, $item->kameti_start_date, $item->kameti_members, $item->kameti_amount,
                               $item->kameti_interest_rate, $item->bid_start_time, $item->bid_end_time, $item->bid_amount_minimum, $item->bid_timer, $item->lucky_draw_amount,
                               $item->lucky_members, $item->runnerup_percentage, $item->kameti_rule, $item->kameti_status);
		$this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$kameti_id = mysqli_stmt_insert_id($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

        if ($result) {
            return $kameti_id;
        } else {
            // kameti failed to create
            return NULL;
        }
    }

	/* Get all kameties for the given user
	 * method GET
	 * @param none
	 * retuen A list of kameties for the given user ID
	 */

	public function getUserAllKameties($user_id){
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

		$stmt = mysqli_prepare($this->conn, "SELECT k.id, k.kameti_name, k.admin_id, k.kameti_start_date, k.kameti_members, k.kameti_amount,
                               k.kameti_interest_rate, k.bid_start_time, k.bid_end_time, k.bid_amount_minimum, k.bid_timer, k.lucky_draw_amount, k.lucky_members,
                               k.runnerup_percentage, k.kameti_rule, k.kameti_status FROM kameti k, kameti_members km WHERE km.kameti_id = k.id AND km.member_id = ?");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'i', $user_id);
		$this->throwExceptionOnError();


		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$rows = array();

		mysqli_stmt_bind_result($stmt, $item->id, $item->kameti_name, $item->admin_id, $item->kameti_start_date, $item->kameti_members, $item->kameti_amount,
                               $item->kameti_interest_rate, $item->bid_start_time, $item->bid_end_time, $item->bid_amount_minimum, $item->bid_timer, $item->lucky_draw_amount,
                               $item->lucky_members, $item->runnerup_percentage, $item->kameti_rule, $item->kameti_status);
		$this->throwExceptionOnError();

		while (mysqli_stmt_fetch($stmt)) {
			$rows[] = $item;
			$item = new stdClass();


			mysqli_stmt_bind_result($stmt, $item->id, $item->kameti_name, $item->admin_id, $item->kameti_start_date, $item->kameti_members, $item->kameti_amount,
                           $item->kameti_interest_rate, $item->bid_start_time, $item->bid_end_time, $item->bid_amount_minimum, $item->bid_timer, $item->lucky_draw_amount,
                           $item->lucky_members, $item->runnerup_percentage, $item->kameti_rule, $item->kameti_status);

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
     * Fetching single kameti
     * method GET
     * @param String $kameti_id id of the kameti
     */
    public function getKameti($kameti_id, $user_id) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();


		$stmt = mysqli_prepare($this->conn, "SELECT k.id, k.kameti_name, k.admin_id, k.kameti_start_date, k.kameti_members, k.kameti_amount,
                               k.kameti_interest_rate, k.bid_start_time, k.bid_end_time, k.bid_amount_minimum, k.bid_timer, k.lucky_draw_amount, k.lucky_members,
                               k.runnerup_percentage, k.kameti_rule, k.kameti_status FROM kameti k, kameti_members km WHERE km.kameti_id = k.id AND km.kameti_id = ? AND km.member_id = ?");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'ii', $kameti_id, $user_id);
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();


		mysqli_stmt_bind_result($stmt, $item->id, $item->kameti_name, $item->admin_id, $item->kameti_start_date, $item->kameti_members, $item->kameti_amount,
                               $item->kameti_interest_rate, $item->bid_start_time, $item->bid_end_time, $item->bid_amount_minimum, $item->bid_timer, $item->lucky_draw_amount,
                               $item->lucky_members, $item->runnerup_percentage, $item->kameti_rule, $item->kameti_status);
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
     * Creating new Kameti
     * method POST
     * @param String $user_id user id to whom kameti belongs to or kameti admin
     * @param String $kameti_name name of the Kameti
     * @param Int $admin_id to whom kameti belongs to or kameti admin
     * @param Date $kameti_start_date Kameti start date
     * @param Int $kameti_members number of kameti members
     * @param Int $kameti_amount total kameti amount
     * @param Flote $kameti_interest_rate Rate of intrest for the given kameti
     * @param Time $bid_start_time It the bid time for kameti auction
     * @param Time $bid_end_time when the bidding will finished
     * @param Int $bid_amount_minimum
     * @param Int $bid_timer
     * @param Int $lucky_draw_amount
     * @param Int $lucky_members
     * @param Int $runnerup_percentage
     * @param Int $kameti_rule
     * @param Array $members_list ("members_list":[{9891533910:"PKR"},{9891533911:"XYZ"}])
     * @return bool id on success and false on failure
     */
    public function updateKameti($item) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET kameti_name = ?,  kameti_start_date = ?, kameti_members = ?, kameti_amount = ?,
                               kameti_interest_rate = ?, bid_start_time = ?, bid_end_time = ?, bid_amount_minimum = ?, bid_timer = ?, lucky_draw_amount = ?, lucky_members = ?,
                               runnerup_percentage = ? WHERE id = ?");
		$this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, 'ssiidssiiiiii', $item->kameti_name, $item->kameti_start_date, $item->kameti_members, $item->kameti_amount,
                               $item->kameti_interest_rate, $item->bid_start_time, $item->bid_end_time, $item->bid_amount_minimum, $item->bid_timer, $item->lucky_draw_amount,
                               $item->lucky_members, $item->runnerup_percentage,  $item->kameti_id);
		$this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

        if ($result) {
            return true;
        } else {
            // kameti failed to update
            return false;
        }
    }

	/**
     * Deleting the kameti from the kameti table if the user is admin of the kameti. and there is no auction againest it.
     * @param int $item
     * @return Bool true/false
     */

    public function deleteKameti($kameti_id, $user_id) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

		// insert query
        $stmt = mysqli_prepare($this->conn, "DELETE FROM $this->tablename WHERE (id=? AND admin_id=?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,'ii', $kameti_id, $user_id);
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
     * Is am I admin of the kameti
     * @param $kameti_id, $admin_id
     * @return Bool true/false
     */

    public function amIAdminOfKameti($kameti_id, $admin_id) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();


		// insert query
        $stmt = mysqli_prepare($this->conn, "SELECT COUNT(*) AS COUNT FROM $this->tablename WHERE (id=? AND admin_id=?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,'ii', $kameti_id, $admin_id);
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
