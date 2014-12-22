<?php

/**
 * This class will have CRUD methods for database tables
 *
 * @author Pramod Kumar Raghav
 *
 */

class auctionsService {

    private $conn;
	private $dbConnect;
	private $tablename;

    function __construct() {
        require_once dirname(__FILE__) . '/dbConnect.php';
        // opening db connection
        $this->dbConnect = new dbConnect();
		$this->tablename = "auction";
    }

    /* ------------- `auction` table method ------------------ */

    /**
     * Creating new Kameti Auction
     * method POST
     * @return bool true/false
     */
    public function createAuction($item) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "INSERT INTO $this->tablename (kameti_id, auction_date, bid_start_time, bid_end_time, auction_winner, auction_runnerup,
							   minimum_bid_amount, maximum_bid_amount, member_profit, interest_rate, status ) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
		$this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, 'isssiiiiiss', $item->kameti_id, $item->auction_date,$item->bid_start_time,$item->bid_end_time, $item->auction_winner, $item->auction_runnerup,
							   $item->minimum_bid_amount, $item->maximum_bid_amount, $item->member_profit, $item->interest_rate, $item->status);
		$this->throwExceptionOnError();

        $result = mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$auction_id = mysqli_stmt_insert_id($stmt);
		$this->throwExceptionOnError();

		mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

        if ($result) {
            return $auction_id;
        } else {
            // kameti failed to create
            return NULL;
        }
    }

	/**
     * Updating Auctions info
     * @param String $mobile User mobile
     */
    public function updateAuction($item) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "UPDATE $this->tablename SET auction_date = ?, bid_start_time = ?, bid_end_time = ?, auction_winner = ?,
											auction_runnerup = ?, minimum_bid_amount = ?, maximum_bid_amount = ?, member_profit = ?, interest_rate = ?, status = ?
											WHERE (kameti_id = ? AND id = ?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt,"sssiiiiidsii", $item->auction_date, $item->bid_start_time, $item->bid_end_time, $item->auction_winner,
							   $item->auction_runnerup, $item->minimum_bid_amount, $item->maximum_bid_amount, $item->member_profit, $item->interest_rate, $item->status,
							   $item->kameti_id, $item->id);
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
     * Delete the kameti Member
     * method DELETE
     * @return bool true/false
     */
    public function deleteAuction($kameti_id, $auctions_id) {
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "DELETE FROM $this->tablename WHERE ( kameti_id = ? AND id = ? )");
		$this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, 'ii', $kameti_id, $auctions_id);
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
     * Checking for isUserExists for the given kameti_id
     * @param String $kameti_id, $auction_id
     * @return boolean
     */
    public function isAuctionExists($kameti_id, $auction_id) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();
        $stmt = mysqli_prepare($this->conn,"SELECT COUNT(*) AS COUNT FROM $this->tablename  WHERE (kameti_id = ? AND id=?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, "ii", $kameti_id, $auction_id);
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
            return true;
        }else{
            return false;
        }
    }


	/**
     * get the member detail by member_id for the given kameti_id
     * @param String $kameti_id, $auction_id
     * @return boolean
     */
    public function getAuctionByID($kameti_id, $auctions_id) {
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();
        $stmt = mysqli_prepare($this->conn, "SELECT * FROM $this->tablename  WHERE (kameti_id = ? AND id=?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, "ii", $kameti_id, $auctions_id);
        $this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();


		mysqli_stmt_bind_result($stmt, $item->id, $item->kameti_id, $item->auction_date, $item->bid_start_time, $item->bid_end_time, $item->auction_winner, $item->auction_runnerup,
								$item->minimum_bid_amount, $item->maximum_bid_amount, $item->member_profit, $item->interest_rate, $item->status);
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

	public function getAllAuction($kameti_id){
		// Get a MySQL DB connection
		$this->conn = $this->dbConnect->connect();

		$stmt = mysqli_prepare($this->conn, "SELECT id, kameti_id, auction_date, bid_start_time, bid_end_time, auction_winner,
                               auction_runnerup, minimum_bid_amount, maximum_bid_amount, member_profit, interest_rate, status FROM $this->tablename WHERE kameti_id = ?");
		$this->throwExceptionOnError();

		mysqli_stmt_bind_param($stmt, 'i', $kameti_id);
		$this->throwExceptionOnError();


		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();

		$rows = array();

		mysqli_stmt_bind_result($stmt, $item->id, $item->kameti_id, $item->auction_date, $item->bid_start_time, $item->bid_end_time, $item->auction_winner,
                               $item->auction_runnerup, $item->minimum_bid_amount, $item->maximum_bid_amount, $item->member_profit, $item->interest_rate,$item->status);
		$this->throwExceptionOnError();

		while (mysqli_stmt_fetch($stmt)) {
			$rows[] = $item;
			$item = new stdClass();
			mysqli_stmt_bind_result($stmt, $item->id, $item->kameti_id, $item->auction_date, $item->bid_start_time, $item->bid_end_time, $item->auction_winner,
                               $item->auction_runnerup, $item->minimum_bid_amount, $item->maximum_bid_amount, $item->member_profit, $item->interest_rate,$item->status);

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
