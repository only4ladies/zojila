<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Pramod Kumar Raghav
 *
 */
class userImageService {

    private $conn;
	private $dbConnect;
	private $tablename;

    function __construct() {
        require_once dirname(__FILE__) . '/dbConnect.php';
		require_once dirname(__FILE__) . '/passHash.php';


        // opening db connection
        $this->dbConnect = new dbConnect();
		$this->tablename = "users_image";
    }


    public function insertImage($imageFile, $user_id) {
		// Get the DB Connection
		$conn = $this->dbConnect->connect();

		$imageFile = mysqli_real_escape_string($conn, $imageFile);
		$query = "INSERT INTO $this->tablename (user_id, user_image) VALUES ($user_id,'$imageFile')";

		mysqli_query($conn,$query);
		mysqli_close($conn);
		return true;
    }


	/**
     * Updating user info
     * @param String $pic User mobile
     */
    public function updateImage ($imageFile, $user_id) {
		// Get the DB Connection
		$conn = $this->dbConnect->connect();

		$imageFile = mysqli_real_escape_string($conn, $imageFile);
		$query = "UPDATE $this->tablename SET user_image='$imageFile' WHERE user_id = $user_id";

		mysqli_query($conn,$query);
		mysqli_close($conn);

		return true;

    }

	/**
     * Updating user info
     * @param String $pic User mobile
     */
    public function getImage ($user_id) {

		$user_image = null;
		// Get the DB Connection
		$this->conn = $this->dbConnect->connect();

        $stmt = mysqli_prepare($this->conn, "SELECT user_image FROM $this->tablename WHERE user_id = ?");
        $this->throwExceptionOnError();


        mysqli_stmt_bind_param($stmt,"i", $user_id);
        $this->throwExceptionOnError();

        mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_bind_result($stmt, $user_image);
		$this->throwExceptionOnError();

		mysqli_stmt_fetch($stmt);
		$this->throwExceptionOnError();


        mysqli_stmt_free_result($stmt);
        mysqli_close($this->conn);

		header('Content-Type: image/jpeg');
		return $user_image;
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
