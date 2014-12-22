<?php

/**
 * @author Pramod Kumar Raghav
 *
 */


/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid device key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route) {
    global $user_id;
	global $device_key;
    // Getting request headers
    $headers = apache_request_headers();
    $response = new stdClass();
    $app = \Slim\Slim::getInstance();

    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
		$item = new stdClass();
        $userService = new userService();


        // get the device key
        $item->device_key = $headers['Authorization'];
		$item->mobile = $headers['mobile'];

        //Get the user id By Mobile number
        $user_info 	= $userService->getUserByMobile($item->mobile);
		if($user_info){
			$user_id 	= $user_info->id;
			if ($user_id != NULL) {
				$user_id = $userService->checkLogin($item);
				$device_key = $item->device_key;

				if($user_id == NULL){
					// Invalid device key
					$response->result = false;
					$response->message = "Invalid Device key";
					echoRespnse(400, $response);
				}
				//$app->stop();
			}else{
				// user not present in users table
				$response->result = false;
				$response->message = "Access Denied. Invalid Device key";
				echoRespnse(401, $response);
			}
		}else{
			$response->result = false;
			$response->message = "Access Denied. User does not exits";
			echoRespnse(400, $response);
		}
    } else {
        // device key is missing in header
        $response->result = false;
        $response->message = "Device key is missing";
        echoRespnse(400, $response);
        //$app->stop();
    }
}

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields, $request_params) {

    $result = true;
    $result_fields = "";
	if(!$request_params){
		$request_params = array();
		$request_params = $GLOBALS['httpdata'];
	}


    foreach ($required_fields as $field) {
        if (!isset($request_params->$field) || strlen(trim($request_params->$field)) <= 0) {
            $result = false;
            $result_fields .= $field . ', ';
        }
    }

    if (!$result) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = new stdClass();
        $app = \Slim\Slim::getInstance();
        $response->result = false;
         $response->message = 'Required field(s) ' . substr($result_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}



/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response->result = false;
        $response->message = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}

function getSession(){
    if (!isset($_SESSION)) {
        session_start();
    }
    $sess = array();
    if(isset($_SESSION['id']))
    {
        $sess["id"] = $_SESSION['id'];
        $sess["name"] = $_SESSION['name'];
        $sess["mobile"] = $_SESSION['mobile'];
		$sess["device_key"] = isset($_SESSION['device_key']) ? $_SESSION['device_key'] : "XYZ";
    }
    else
    {
        $sess["id"] = '';
        $sess["name"] = 'Guest';
        $sess["mobile"] = '';
    }
    return $sess;
}

 function destroySession(){
    if (!isset($_SESSION)) {
    session_start();
    }
    if(isSet($_SESSION['id']))
    {
        unset($_SESSION['id']);
        unset($_SESSION['name']);
        unset($_SESSION['mobile']);
        $info='info';
        if(isSet($_COOKIE[$info]))
        {
            setcookie ($info, '', time() - $cookie_time);
        }
        $msg="Logged Out Successfully...";
    }
    else
    {
        $msg = "Not logged in...";
    }
    return $msg;
}

function generateRandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

?>
