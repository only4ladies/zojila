<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Pramod Kumar Raghav
 *
 */
class bidService {

    private $conn;
	private $dbConnect;
	private $tablename;

    function __construct() {
        require_once dirname(__FILE__) . '/dbConnect.php';
		require_once dirname(__FILE__) . '/passHash.php';


        // opening db connection
        $this->dbConnect = new dbConnect();
		$this->tablename = "bid";
    }


    /* ------------- `bid` table method ------------------ */
	public function createBid($item) {
		// Get a MySQL DB connection
		// CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_bid`
		// (IN `auctionid` INT UNSIGNED, IN `kametiid` INT UNSIGNED, IN `bidamount` INT UNSIGNED, IN `memberid` INT UNSIGNED, IN `bidtime` TIME, IN `interestrate` FLOAT UNSIGNED)
		$this->conn = $this->dbConnect->connect();

		$stmt = mysqli_prepare($this->conn, "CALL kameti.insert_bid (?,?,?,?,?,?)");
		$this->throwExceptionOnError();

		date_default_timezone_set(TIMEZONE);
		$item->bid_time = date('Y-m-d H:i:s', time());

        mysqli_stmt_bind_param($stmt, 'iiiiss', $item->auction_id, $item->kameti_id, $item->bid_amount, $item->member_id, $item->bid_time, $item->interest_rate);
        $result = mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

        return true;

	}

	/**
     * Updating user info
     * @param String $mobile User mobile
     */
    public function updateBid($item) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET bid_status = ? WHERE (kameti_id = ? AND auction_id = ? AND id = ?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"siii", $item->bid_status, $item->kameti_id, $item->auction_id, $item->bid_id);
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

	public function getBidByID($item){
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

		$stmt = mysqli_prepare($this->conn, "SELECT id, auction_id, kameti_id, member_id, bid_time, bid_amount, interest_rate, bid_status
							   FROM $this->tablename WHERE ( kameti_id = ? AND auction_id = ? AND id = ?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'iii', $item->kameti_id, $item->auction_id, $item->id);
		$this->throwExceptionOnError();


		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();


		mysqli_stmt_bind_result($stmt, $item->id, $item->auction_id, $item->kameti_id, $item->member_id, $item->bid_time, $item->bid_amount, $item->interest_rate, $item->bid_status);
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
            return false;
        }
	}

    /* Get all the users for the given kameti if member_id is the member of the given kameti_id
	 * method GET
	 * @param none
	 * retuen A list of kameties for the given member_id
	 */

	public function getAllBids($kameti_id, $auction_id){
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

		$stmt = mysqli_prepare($this->conn, "SELECT id, auction_id, kameti_id, member_id, bid_time, bid_amount, interest_rate, bid_status
							   FROM $this->tablename WHERE ( kameti_id = ? AND auction_id = ?)");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'ii', $kameti_id, $auction_id);
		$this->throwExceptionOnError();


		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$rows = array();

		mysqli_stmt_bind_result($stmt, $item->id, $item->auction_id,$item->kameti_id, $item->member_id, $item->bid_time, $item->bid_amount, $item->interest_rate, $item->bid_status);
		$this->throwExceptionOnError();

		while (mysqli_stmt_fetch($stmt)) {

			$rows[] = $item;
			$item = new stdClass();

			mysqli_stmt_bind_result($stmt, $item->id, $item->auction_id, $item->kameti_id,$item->member_id, $item->bid_time, $item->bid_amount, $item->interest_rate, $item->bid_status);

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
