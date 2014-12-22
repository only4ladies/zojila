<?php
/**
 *
 * @author Pramod Kumar Raghav
 *
 */

date_default_timezone_set('Asia/Kolkata');

require_once '../include/utilityService.php';
require_once '../include/userService.php';
require_once '../include/deviceService.php';
require_once '../include/userVerifyService.php';
require_once '../include/kametiService.php';
require_once '../include/membersService.php';
require_once '../include/auctionsService.php';

require_once '../include/gcmService.php';
require_once '../include/GCM.php';

require_once '../include/passHash.php';
require_once '../libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

//Enable logging
//$app->log->setEnabled(true);
//$app->log->setLevel(\Slim\Log::DEBUG);



// User id from users db - Global Variable
$user_id = NULL;
$device_key = NULL;

/**
 * ----------- METHODS WITHOUT AUTHENTICATION ---------------------------------
 */

 /**
 * get the current time of the server
 * url - /currenttime
 * method - GET
 */

$app->get('/currenttime', function() use ($app) {

        $response = new stdClass();
        $response->result = true;
        $response->current_time = date('Y-m-d H:i:s', time());

        echoRespnse(200, $response);
    });

 /**
 * get the product key from the server
 * url - /productLicenseKey
 * method - GET
 */

$app->get('/productLicenseKey', function() use ($app) {
        require_once '../include/playStoreService.php';
        require_once '../include/inappProductService.php';

        $response = new stdClass();
        $item = new stdClass();

        $playStoreService = new playStoreService();
        $inappProductService = new inappProductService();

        $item = $playStoreService->getProductLicenseKey();
        $item->version_code = $playStoreService->getVersionCode();

        if ($item){
            $response = $item;
            // Let get the Inapp Products
            $rows = $inappProductService->getInAppProducts();
            if($rows){
                $response->InAppProducts = $rows;
                $response->result = true;
                $response->current_time = date('Y-m-d H:i:s', time());
            }else{
                $response->result = false;
                $response->message = "Failed to get inapp product info";
            }
        }else{
            $response->result = false;
            $response->message = "Failed to get product license";
        }

        echoRespnse(200, $response);
    });


/**
 * Send the SMS on the given mobile
 * url - /sendsms
 * method - POST
 * params - mobile, device_key
 * (mobile) are the required parameters
 */

$app->post('/sendsms', function() use ($app) {
        global $httpdata;
        $input_data = file_get_contents("php://input");
        $httpdata = json_decode($input_data);



        $response = new stdClass();
        $item = new stdClass();

        $userVerifyService = new userVerifyService();

        // reading post params
        $item->mobile         = isset($httpdata->mobile) ? $httpdata->mobile : NULL;
        $item->message         = isset($httpdata->message) ? $httpdata->message : NULL;

        $userVerifyService->sendVerificationCode($item);
        $response->result = true;
        $response->message = "sms send on your mobile number";


        // echo json response
        echoRespnse(201, $response);
    });

$app->post('/sendemail', function() use ($app) {
        global $httpdata;
        $input_data = file_get_contents("php://input");
        $httpdata = json_decode($input_data);

        $response = new stdClass();
        $item = new stdClass();

       $item = $httpdata->customer;

	   /*
		   $item->name  = "pramod raghav";
		   $item->mobile = "9891533910";
		   $item->email = "pramod.raghav@yahoo.com";
		   $item->message = "Hello";
		*/

        $to      = 'prak.firm@gmail.com';
        $subject = "Contact US";

        $message = "Name : $item->name" . "\n";
        $message = $message. "Mobile : $item->mobile" .  "\n";;
        $message = $message. "Email : $item->email" . "\n";;
        $message = $message. "message : $item->message" . "\n";;

        $headers = "From: $item->email" . "\r\n" .
            "Reply-To: $item->email" . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);

        $response->result = true;
        $response->message = "Thanks. We will revert back soon.";


        // echo json response
        echoRespnse(201, $response);
    });

/**
 * User Login
 * url - /login
 * method - GET
 * params - mobile, device_key, password
 * * (mobile, device_key) are the required parameters
 */
$app->get('/login', 'authenticate', function() use ($app) {
        global $user_id;
        global $device_key;

        require_once '../include/passHash.php';
        $response = new stdClass();
        $userService = new userService();

        $response->user_id = $user_id;

        $user = $userService->getUserByID($user_id);
        if ($user != NULL) {
            $response = $user;
            $response->result = true;
            $response->user_id = $user_id;
            $response->device_key = $device_key;
            $response->message = "Successfully login get!";
        } else {
            // unknown error occurred
            $response->result = false;
            $response->message = "An error occurred. Please try again";
        }

        echoRespnse(200, $response);
     });

$app->post('/login', function() use ($app) {
        require_once '../include/passHash.php';
        $r = json_decode($app->request->getBody());

        $input_data = file_get_contents("php://input");
        $httpdata = json_decode($input_data);

        verifyRequiredParams(array('mobile', 'password_hash'),$httpdata->customer);

        $password_hash = $r->customer->password_hash;
        $mobile = $r->customer->mobile;

        $response = new stdClass();
        $userService = new userService();

        $password_hash = "";
        $user = $userService->getUserByMobile($mobile);
        if ($user != NULL) {
            if(passHash::check_password($user->password_hash,$password_hash)){
                //Let get the device key to do other activity

                $response = $user;
                $response->status = "success";
                $response->message = "Successfully login!";

                $deviceService = new deviceService();
                $response->device_key = $deviceService->getDeviceKeyByUserID($user->id);

                if (!isset($_SESSION)) {
                    session_start();
                }
                $_SESSION['id'] = $user->id;
                $_SESSION['mobile'] = $mobile;
                $_SESSION['name'] = $user->name;
                $_SESSION['device_key'] = $response->device_key;
            }else {
                $response->status = "error";
                $response->message = 'Login failed. Incorrect credentials';
            }

        } else {
            $response->status = "error";
            $response->message = 'No such user is registered';
        }

        echoRespnse(200, $response);
     });
/**
 * url - /register
 * method - POST
 * params - mobile, device_key
 * (name, mobile, device_key) are the required parameters
 */
$app->post('/register', function() use ($app) {
        global $httpdata;
        require_once '../include/playStoreService.php';


        $input_data = file_get_contents("php://input");
        $httpdata = json_decode($input_data);

        $headers = apache_request_headers();
        $httpdata->device_key = $headers['Authorization'];

        // check for required params
        verifyRequiredParams(array('name', 'mobile', 'device_key'),null);

        $playStoreService = new playStoreService();

        $response = new stdClass();
        $item = new stdClass();


        // reading post params
        $item->name                 = isset($httpdata->name) ? $httpdata->name : NULL;
        $item->mobile               = isset($httpdata->mobile) ? $httpdata->mobile : NULL;
        $item->email                = isset($httpdata->email) ? $httpdata->email : NULL;
        $item->password             = isset($httpdata->password) ? $httpdata->password : NULL;
        $item->developer_payload    = isset($httpdata->developer_payload) ? $httpdata->developer_payload : NULL;
        $item->device_key           = isset($httpdata->device_key) ? $httpdata->device_key : NULL;
        $item->created_at           = isset($httpdata->created_at) ? $httpdata->created_at : date('Y-m-d H:i:s', time());


        if(strchr($item->device_key,"SERVICE_NOT_AVAILABLE")){
            $item->device_key  = NULL;
        }
        // get trial period
        $item->trial_period = $playStoreService->getTrialPeriod();


        // validating email address
        if(isset($item->email)){
            validateEmail($item->email);
        }

        $userService = new userService();
        $deviceService = new deviceService();

        $res = $userService->createUser($item);

        if ($res == USER_CREATED_SUCCESSFULLY) {
            //Get the user id By Mobile number
            $user_info = $userService->getUserByMobile($item->mobile);
            $item->user_id = $user_info->id;
            // Add the device_key into the device table

			if(!$deviceService->isDeviceKeyExists($item)){
				if($deviceService->createDevice($item)){
					$response->result = true;
                    $response->message = "Your mobile number and device_key is successfully registered";
				}else{
				 $response->result = false;
                $response->message = "Oops! An error occurred while registereing device";
                }
			}else{
				$response->result = true;
                $response->message = "Your mobile  number is successfully registered";
			}
        } else if ($res == USER_CREATE_FAILED) {
            $response->result = false;
            $response->message = "Oops! An error occurred while registereing";
        } else if ($res == USER_ALREADY_EXISTED) {

            $user_info = $userService->getUserByMobile($item->mobile);
            $item->user_id = $user_info->id;
            $item->member_id = $user_info->id;

            //Let check if the user device is also registered.
            if(!$deviceService->isDeviceKeyExists($item)){
				if($deviceService->createDevice($item)){
					$response->result = true;
                    $response->message = "Your mobile number and device_key is successfully registered";
				}else{
				$response->result = false;
                $response->message = "Oops! An error occurred while registereing device";
                }
			}
            if(!$user_info->created_at){
                $userService->updateCreatedAT($item);
				$response->result = true;
			}

            if(!$user_info->trial_period){
                $userService->updateTrialPeriod($item);
				$response->result = true;
			}

            if(!$user_info->developer_payload){
                $userService->updateDeveloperPayload($item);
				$response->result = true;
			}
        }
        // echo json response
        echoRespnse(200, $response);
    });


/*
 * ------------------------ METHODS WITH AUTHENTICATION ------------------------
 */

$app->post('/members/info/:member_id', 'authenticate', function($member_id) use ($app){
        global $user_id;
        global $httpdata;

        $input_data = file_get_contents("php://input");
        $httpdata = json_decode($input_data);

        $response = new stdClass();
        $item = new stdClass();

        $response->result = false;

        // reading put prams
        $item->mobile    = isset($httpdata->mobile) ? $httpdata->mobile : NULL;
        $item->name      = isset($httpdata->name) ? $httpdata->name : NULL;
        $item->email     = isset($httpdata->email) ? $httpdata->email : NULL;
        $item->developer_payload     = isset($httpdata->developer_payload) ? $httpdata->developer_payload : NULL;


        $item->member_id  = $member_id;                    //$member_id

        // Creating User Service Object
        $userService = new userService();
        // Creating Kameti_User Service Object
        $membersService = new membersService();

        // First update the user info in user table
        if(isset($item->name)){
            $res = $userService->updateUserName($item);
            $res = $membersService->updateUserName($item);
        }
        if(isset($item->email)){
            $res = $userService->updateUserEmail($item);
        }
        if(isset($item->mobile)){
            $res = $userService->updateUserMobile($item);
        }

        if(isset($item->developer_payload)){
             $res = $userService->updateDeveloperPayload($item);
        }

        $member = $userService->getUserByID($member_id);
        if($member != NULL){
            $response = $member;
            $response->user_id = $member_id;
            $response->result = true;
        }

        echoRespnse(200, $response);
 });



 /**
 * Listing all kameties of particual user
 * method GET
 * url /kameties
 */

$app->get('/kameties', 'authenticate', function() use ($app) {
            global $user_id;

            $response = array();
            $item = new stdClass();

            $item->user_id = $user_id;

            // fetching all user tasks
            $kametiService = new kametiService();
            $result = $kametiService->getUserAllKameties($item->user_id);

            if(is_array($result)){
                $response = $result;

            }else{
                $response["result"] = false;
                $response["message"] = "You don't have any kameti";
            }

            echoRespnse(200, $response);
        });


/**
 * Creating new kameti in db
 * method POST
 * params - mobile, kameti_name, kameti_start_date, kameti_members, kameti_amount, kameti_interest_rate, bid_start_time,
 *          bid_end_time, bid_amount_minimum, bid_timer, lucky_draw_amount, lucky_members, runnerup_percentage, kameti_rule
 * url - /kameties/
 * Required Parameters : 'kameti_name', 'kameti_start_date', 'kameti_members', 'kameti_amount', 'kameti_interest_rate'
 */




$app->post('/kameties', 'authenticate', function() use ($app) {
        global $user_id;
        global $httpdata;
        $input_data = file_get_contents("php://input");
        $httpdata = json_decode($input_data);



        // check for required params
        verifyRequiredParams(array('kameti_name', 'kameti_start_date', 'kameti_members', 'kameti_amount', 'kameti_interest_rate'),null);

        $item = new stdClass();

        // reading post params
        $item->kameti_name            = isset($httpdata->kameti_name) ? $httpdata->kameti_name : NULL;
        $item->kameti_start_date      = isset($httpdata->kameti_start_date) ? $httpdata->kameti_start_date : NULL;
        $item->kameti_members         = isset($httpdata->kameti_members) ? $httpdata->kameti_members : NULL;
        $item->kameti_amount          = isset($httpdata->kameti_amount) ? $httpdata->kameti_amount : NULL;
        $item->kameti_interest_rate   = isset($httpdata->kameti_interest_rate) ? $httpdata->kameti_interest_rate : NULL;

        $item->bid_start_time         = isset($httpdata->bid_start_time) ? $httpdata->bid_start_time : NULL;
        $item->bid_end_time           = isset($httpdata->bid_end_time) ? $httpdata->bid_end_time : NULL;
        $item->bid_amount_minimum     = isset($httpdata->bid_amount_minimum) ? $httpdata->bid_amount_minimum : NULL;
        $item->bid_timer              = isset($httpdata->bid_timer) ? $httpdata->bid_timer: NULL;

        $item->lucky_draw_amount      = isset($httpdata->lucky_draw_amount) ? $httpdata->lucky_draw_amount : NULL;
        $item->lucky_members          = isset($httpdata->lucky_members) ? $httpdata->lucky_members : NULL;

        $item->runnerup_percentage    = isset($httpdata->runnerup_percentage) ? $httpdata->runnerup_percentage : NULL;
        $item->kameti_rule            = isset($httpdata->kameti_rule) ? $httpdata->kameti_rule : 1;
        $item->kameti_status          = isset($httpdata->kameti_status) ? $httpdata->kameti_status : "Created";


		$response = new stdClass();

		$item->user_id = $user_id;

        //Let Define the Admin ID,. Normally Admin the person who create the new kameti
		$item->admin_id = $item->user_id;


        $kametiService = new kametiService();

        // creating new kameti
        $kameti_id = $kametiService->createKameti($item);

        if ($kameti_id != NULL) {
            $userService = new userService();
            $user_info = $userService->getUserByID($user_id);

            $item->kameti_id = $kameti_id;
            $item->member_name = $user_info->name;

            $item->member_id = $user_id;

            //Kameti is created successfuly let now add the default member for the kameti. Admin is the default member
            $membersService = new membersService();

            $member_id = $membersService->createMember($item);

            if($member_id == NULL){
                $response->result = false;
                $response->message = "Failed to create the default kameti memebr";
            } else {
                $mykameti = $kametiService->getKameti($kameti_id, $user_id);
                $response = $mykameti;
                $response->result = true;
                $response->message = "Kameti created successfully";

            }
            echoRespnse(201, $response);
        } else {
            $response->result = false;
            $response->message = "Failed to create kameti. Please try again";
            echoRespnse(200, $response);
        }
    });




/**
 * Listing single kameti of particual user
 * method GET
 * url /kameties/:kameti_id
 * Will return 404 if the kameti doesn't belongs to user
 */

$app->get('/kameties/:kameti_id', 'authenticate', function($kameti_id) use($app) {
            global $user_id;

            $response = new stdClass();
            $item = new stdClass();

            $item->user_id = $user_id;

            // fetching $kameti_id from kameti if $user_id is the member of this kameti
            $kametiService = new kametiService();

            // fetch kameti
            $result = $kametiService->getKameti($kameti_id, $user_id);

            if ($result != NULL) {
                $response->result = true;
                $response->kameties = $result;
            } else {
                $response->result = false;
                $response->message = "The requested resource doesn't exists or doesn't belong to you";
            }
            echoRespnse(200, $response);
        });



/**
 * Updating existing kameti. If the user is the Admin of the given kameti
 * method POST
 * params kameti, status
 * url - /kameties/:kameti_id
 */

$app->post('/kameties/:kameti_id', 'authenticate', function($kameti_id) use($app) {
            global $user_id;
            global $httpdata;
            $input_data = file_get_contents("php://input");
            $httpdata = json_decode($input_data);

            $response = new stdClass();
            $item = new stdClass();

            $item->user_id = $user_id;

            // reading put params

            $item->kameti_name            = isset($httpdata->kameti_name) ? $httpdata->kameti_name : NULL;
            $item->kameti_start_date      = isset($httpdata->kameti_start_date) ? $httpdata->kameti_start_date : NULL;
            $item->kameti_members         = isset($httpdata->kameti_members) ? $httpdata->kameti_members : NULL;
            $item->kameti_amount          = isset($httpdata->kameti_amount) ? $httpdata->kameti_amount : NULL;
            $item->kameti_interest_rate   = isset($httpdata->kameti_interest_rate) ? $httpdata->kameti_interest_rate : NULL;

            $item->bid_start_time         = isset($httpdata->bid_start_time) ? $httpdata->bid_start_time : NULL;
            $item->bid_end_time           = isset($httpdata->bid_end_time) ? $httpdata->bid_end_time : NULL;
            $item->bid_amount_minimum     = isset($httpdata->bid_amount_minimum) ? $httpdata->bid_amount_minimum : NULL;
            $item->bid_timer              = isset($httpdata->bid_timer) ? $httpdata->bid_timer : NULL;

            $item->lucky_draw_amount      = isset($httpdata->lucky_draw_amount) ? $httpdata->lucky_draw_amount : NULL;
            $item->lucky_members          = isset($httpdata->lucky_members) ? $httpdata->lucky_members : NULL;

            $item->runnerup_percentage    = isset($httpdata->runnerup_percentage) ? $httpdata->runnerup_percentage : NULL;
            $item->kameti_rule            = isset($httpdata->kameti_rule) ? $httpdata->kameti_rule : NULL;

            $item->kameti_status          = isset($httpdata->kameti_status) ? $httpdata->kameti_status : NULL;

            $item->kameti_id              = $kameti_id;

            $kametiService = new kametiService();
            $result = $kametiService->updateKameti($item);

            if ($result) {
                // fetch kameti
                $result = $kametiService->getKameti($kameti_id, $user_id);
                if ($result != NULL) {
                    $response = $result;
                    $response->id = $kameti_id;
                    $response->result = true;
                    $response->message = "Kameti updated successfully";
                } else {
                    $response->result = false;
                    $response->message = "The requested resource doesn't exists or doesn't belong to you";
                }
            } else {
                // task failed to update
                $response->result = false;
                $response->message = "Kameti failed to update. Please try again!";
            }
            echoRespnse(200, $response);
        });


/**
 * Deleting kameti. Admin user can delete only their kameties
 * method DELETE
 * url /kameties/:kameti_id
 */

$app->delete('/kameties/:kameti_id', 'authenticate', function($kameti_id) use($app) {
            global $user_id;
            $response = new stdClass();

            $kametiService = new kametiService();
            $result = $kametiService->deleteKameti($kameti_id, $user_id);

            if ($result) {
                // Kameti deleted successfully
                $response->result = true;
                $response->message = "Kameti deleted succesfully";
            } else {
                // Kameti failed to delete
                $response->result = false;
                $response->message = "Kameti failed to delete. Please try again!";
            }
            echoRespnse(200, $response);
        });






/**
 * Adding users for the given kameti_id. Admin user can add only their kameti users
 * method POST
 * url /kameties/:kameti_id/members
 * @param mobile Kameti user mobile number
 * @param name Name of the Kameti user
 */

$app->post('/kameties/:kameti_id/members', 'authenticate', function($kameti_id) use($app) {
        global $user_id;
        global $httpdata;
        $input_data = file_get_contents("php://input");
        $httpdata = json_decode($input_data);

        $response = new stdClass();
        $item = new stdClass();

        $item->user_id = $user_id;

        // reading put prams

        $item->mobile    = isset($httpdata->mobile) ? $httpdata->mobile : NULL;
        $item->name      = isset($httpdata->name) ? $httpdata->name : NULL;

        $item->kameti_id = $kameti_id;                      //kameti ID
        $item->admin_id  = $user_id;                       //admin ID

        // Creating User Service Object
        $userService = new userService();

        // Creating Kameti_User Service Object
        $membersService = new membersService();

        // Creating Kameti Service Object
        $kametiService = new kametiService();

        //Does this kameti exists and the user is the kameti admin.
        $result = $kametiService->amIAdminOfKameti($kameti_id, $user_id);

        if ($result) {
            //Create kameti user if not already exists
            $result = $userService->createUser($item);

            if ($result == USER_CREATE_FAILED) {
                $response->result = false;
                $response->message = "Oops! An error occurred while registering";
            }else{
                // Get the user id
                $result = $userService->getUserByMobile($item->mobile);
                if($result != NULL){
                    $item->member_id = $result->id;

                    if(!isset($item->name)){
                        if($result->name){
                            $item->member_name = $result->name;
                        }
                    }else{
                        $item->member_name = $item->name;
                    }
                    // Now we finished all the check before adding new user for the given kameti
                    // Before adding new user into kameti_user let confirm user does not exits for the given kameti.

                    if($membersService->isMemberExists($item->kameti_id, $item->member_id)){
                        $response->result = false;
                        $response->message = "User Already Exists for this kameti";
                    }else{
                        // Let add new member for the kameti as he/she does not exists
                        $member_id = $membersService->createMember($item);
                        if($member_id == NULL){
                            $response->result = false;
                            $response->message = "Failed to add user for this kameti";
                        }else{
                            $member = $membersService->getMemberByID($member_id);
                            $response = $member;
                        }
                    }
                }else{
                    $response->result = false;
                    $response->message = "Oops! Failed to fetch to user information from users for $item->mobile";
                }
            }
        } else {
            // Kameti failed to delete
            $response->result = false;
            $response->message = "You are not the kamti admin. Or kameti does not exist!";
        }
        echoRespnse(200, $response);
    });




/**
 * Get All users for the given kameti_id. If user is the member of the kameti.
 * method GET
 * url /kameties/:kameti_id/members
 */

$app->get('/kameties/:kameti_id/members', 'authenticate', function($kameti_id) use($app) {
        global $user_id;

        $response = new stdClass();
        $item = new stdClass();


        // Creating User Service Object
        $userService = new userService();

        // Creating Kameti_User Service Object
        $membersService = new membersService();


        // Creating Kameti Service Object
        $kametiService = new kametiService();


        // Let first validate the the user exists for this kamti
        $result = $membersService->isMemberExists($kameti_id, $user_id);

        if ($result) {
            $result = $membersService->getAllMembers($kameti_id);
            if(is_array($result)){
                $response = $result;
            }else{
                $response["result"] = false;
                $response["message"] = "You don't have any kameti";
            }
        } else {
            // Failed to get the members list for the given kameti as you are not the user for this kameti
            $response->result = false;
            $response->message = "You are not the kamti user or Kameti does not exists!";
        }
        echoRespnse(200, $response);
    });


/**
 * Update the users info for the given kameti_id. If user is the member/admin of the kameti.
 * method POST
 * url /kameties/:kameti_id/members/:member_id
 */

$app->post('/kameties/:kameti_id/members/:member_id', 'authenticate', function($kameti_id, $member_id) use($app) {
        global $user_id;
        global $httpdata;


        $input_data = file_get_contents("php://input");
        $httpdata = json_decode($input_data);

        $response = new stdClass();
        $item = new stdClass();

        $item->user_id = $user_id;

        // reading put prams
        $item->mobile    = isset($httpdata->mobile) ? $httpdata->mobile : NULL;
        $item->name      = isset($httpdata->name) ? $httpdata->name : NULL;
        $item->email     = isset($httpdata->email) ? $httpdata->email : NULL;


        // Admin permissions
        $item->can_admin_update_name    = isset($httpdata->can_admin_update_name) ? $httpdata->can_admin_update_name : NULL;
        $item->can_admin_update_mobile  = isset($httpdata->can_admin_update_mobile) ? $httpdata->can_admin_update_mobile : NULL;
        $item->can_admin_update_email   = isset($httpdata->can_admin_update_email) ? $httpdata->can_admin_update_email : NULL;
        $item->can_admin_update_pic     = isset($httpdata->can_admin_update_pic) ? $httpdata->can_admin_update_pic : NULL;
        $item->can_admin_update_bid     = isset($httpdata->can_admin_update_bid) ? $httpdata->can_admin_update_bid : NULL;

        // Others parameters
        $item->kameti_id = $kameti_id;                     //kameti ID
        $item->admin_id  = $user_id;                       //admin ID
        $item->member_id  = $member_id;                    //$member_id

        // Creating User Service Object
        $userService = new userService();
        // Creating Kameti_User Service Object
        $membersService = new membersService();
        // Creating Kameti Service Object
        $kametiService = new kametiService();

        //Does user is the kameti admin.
        $result = $kametiService->amIAdminOfKameti($kameti_id, $user_id);
        if ($result) {
            // Before updating user info for kameti_member let confirm user does not exits for the given kameti.
            $result = $membersService->getMemberByID($item->kameti_id, $item->member_id);
            if($result != NULL){
                // First update the user info in user table
                if(($result->can_admin_update_name) and isset($item->name)){
                    $res = $userService->updateUserName($item);
                    $res = $membersService->updateUserName($item);
                }
                if(($result->can_admin_update_email) and isset($item->email)){
                    $res = $userService->updateUserEmail($item);
                }
                if(($result->can_admin_update_mobile) and isset($item->mobile)){
                    $res = $userService->updateUserMobile($item);
                }
                if(($result->can_admin_update_pic) and isset($item->pic)){
                    $res = $userService->updateUserPic($item);
                }
                $response->result = true;
                $response->message = "Admin updated user info for this kameti";
            }else{
                $response->result = false;
                $response->message = "User doesn't exists for this kameti";
            }
        } elseif ($user_id == $member_id){
            if(isset($item->can_admin_update_name)){
                $result = $membersService->changeUserNamePermission($item);
            }
            if(isset($item->can_admin_update_mobile)){
                $result = $membersService->changeUserMobilePermission($item);
            }
            if(isset($item->can_admin_update_email)){
                $result = $membersService->changeUserEmailPermission($item);
            }
            if(isset($item->can_admin_update_pic)){
                $result = $membersService->changeUserPicPermission($item);
            }
            if(isset($item->can_admin_update_bid)){
                $result = $membersService->changeUserBidPermission($item);
            }
            // Kameti failed to delete
            $response->result = true;
            $response->message = "Updating admin permissions!";
        } else {

            // Kameti failed to delete
            $response->result = false;
            $response->message = "You are not the kamti admin. Or kameti does not exist!";
        }

        if($response->result){
            $member = $userService->getUserByID($member_id);
            if($member != NULL){
                $response = $member;
                $response->user_id = $member_id;
                $response->result = true;
            }
        }
        echoRespnse(200, $response);
    });

/**
 * Get Kameti members info. If user is the member of the kameti.
 * method GET
 * url /kameties/:kameti_id/members/:member_id
 */

$app->get('/kameties/:kameti_id/members/:member_id', 'authenticate', function($kameti_id, $member_id) use($app) {
        global $user_id;

        $response = new stdClass();
        $item = new stdClass();


        // Creating User Service Object
        $userService = new userService();

        // Creating Kameti_User Service Object
        $membersService = new membersService();


        // Creating Kameti Service Object
        $kametiService = new kametiService();


        // Let first validate the the user exists for this kamti
        $result = $membersService->isMemberExists($kameti_id, $user_id);

        if ($result) {
            $member = $userService->getUserByID($member_id);
            if($member != NULL){
                $response = $member;
                $response->user_id = $member_id;
                $response->result = true;
                $response->members_info = $result;
            }else{
                // Failed to get the users list for the given kameti as you are not the user for this kameti
                $response->result = false;
                $response->message = "Failed to get the member's info!";
            }

        } else {
            // Failed to get the users list for the given kameti as you are not the user for this kameti
            $response->result = false;
            $response->message = "You are not the kamti user or Kameti does not exists!";
        }
        echoRespnse(200, $response);
    });


/**
 * Delete the users of the given kameti_id. If user is the admin of the kameti.
 * method DELETE
 * url /kameties/:kameti_id/members/:member_id
 */

$app->delete('/kameties/:kameti_id/members/:member_id', 'authenticate', function($kameti_id, $member_id) use($app) {
        global $user_id;

        $response = new stdClass();

        // Creating User Service Object
        $userService = new userService();

        // Creating Kameti_User Service Object
        $membersService = new membersService();


        // Creating Kameti Service Object
        $kametiService = new kametiService();


        //Does user is the kameti admin.
        $result = $kametiService->amIAdminOfKameti($kameti_id, $user_id);

        if ($result) {
            //Now let me delete the kameti member as I am the admin of this kameti
            $result = $membersService->deleteMember($kameti_id, $member_id);
            if ($result) {
                $response->result = true;
                $response->message = "Kameti member deleted successfully!";
            }else{
                $response->result = false;
                $response->message = "Oops! Failed to delete the kameti member";
            }
        } else {
            // Kameti failed to delete
            $response->result = false;
            $response->message = "You are not the kamti admin. Or kameti does not exist!";
        }
        echoRespnse(200, $response);
    });



/**
 * Get Member's image.
 * method GET
 * url /members/image/:member_id
 */

$app->get('/members/image/:member_id',  function($member_id) use($app) {

        require_once '../include/userImageService.php';
        // Creating User Service Object
        $userImageService = new userImageService();

        $result = $userImageService->getImage($member_id);
        $res = $app->response();
        $res['Content-Type'] = 'image/png';
        echo $result;
    });


/**
 * User Profile Update
 * url - '/members/image/:member_id'
 * method - POST
 * params - mobile, device_key, password
 * * (mobile, device_key) are the required parameters
 */
$app->post('/members/image/:member_id', 'authenticate', function($member_id) use ($app) {
        global $user_id;
        global $device_key;

        global $httpdata;



        require_once '../include/userImageService.php';

        $response = new stdClass();
        $item = new stdClass();

        $handle = fopen ($_FILES['uploaded_file']['tmp_name'], "r");
        $image = fread ($handle, filesize($_FILES['uploaded_file']['tmp_name']));
        fclose ($handle);

        $userImageService = new userImageService();

        $result = $userImageService->getImage($member_id);
        if($result){
            $result_update = $userImageService->updateImage($image, $member_id);
            if($result_update){
                $response->result = true;
                $response->message = "Successfully updated the profile pic";
            }else {
                $response->result = false;
                $response->message = "Failed to Update the user image";
            }
        } else {
            $result_insert = $userImageService->insertImage($image, $member_id);
            if($result_insert){
                $response->result = true;
                $response->message = "Successfully Inserted the user image";
            }else {
                $response->result = false;
                $response->message = "Failed to Insert the user image";
            }
        }

        $gcmService = new gcmService();
        $gcmService->notifyPofileImageChange($member_id);

        echoRespnse(200, $user_id);
     });


/**
 * Adding auction for the given kameti_id. Admin user can add only auction for the given kameti
 * method POST
 * url /kameties/:kameti_id/auction
 */

$app->post('/kameties/:kameti_id/auctions', 'authenticate', function($kameti_id) use($app) {
        global $user_id;
        global $httpdata;
        $input_data = file_get_contents("php://input");
        $httpdata = json_decode($input_data);

        $response = new stdClass();
        $item = new stdClass();

        $item->user_id = $user_id;

        // reading put prams
        $item->auction_date         = isset($httpdata->auction_date) ? $httpdata->auction_date : NULL;
        $item->bid_start_time       = isset($httpdata->bid_start_time) ? $httpdata->bid_start_time : NULL;
        $item->bid_end_time         = isset($httpdata->bid_end_time) ? $httpdata->bid_end_time : NULL;

        $item->auction_winner       = isset($httpdata->auction_winner) ? $httpdata->auction_winner : 0;
        $item->auction_runnerup     = isset($httpdata->auction_runnerup) ? $httpdata->auction_runnerup : 0;


        $item->minimum_bid_amount   = isset($httpdata->minimum_bid_amount) ? $httpdata->minimum_bid_amount : 0;
        $item->maximum_bid_amount   = isset($httpdata->maximum_bid_amount) ? $httpdata->maximum_bid_amount : 0;
        $item->member_profit        = isset($httpdata->member_profit) ? $httpdata->member_profit : 0;
        $item->interest_rate        = isset($httpdata->interest_rate) ? $httpdata->interest_rate : 0;
        $item->status               = isset($httpdata->status) ? $httpdata->status : "Pending";


        // Others parameters
        $item->kameti_id = $kameti_id;                     //kameti ID

        // Creating Kameti_User Service Object
        $membersService = new membersService();
        // Creating Kameti Service Object
        $kametiService = new kametiService();
        // Let first validate the the user exists for this kamti
        $result = $membersService->isMemberExists($kameti_id, $user_id);
        if ($result) {
            // Now add the new auction for this kameti
            $auctionsService = new auctionsService();
            $auction_id = $auctionsService->createAuction($item);
            if ($auction_id){
                // Let get the auction bye auction_id
                $item = $auctionsService->getAuctionByID($kameti_id,$auction_id);
                if($item){
                    $response = $item;
                    // Successfully create auction for this kameti
                    $response->result = true;
                    $response->message = "Successfully create auction for this kameti!";
                }else{
                    // Failed to get the auction bye auction_id
                    $response->result = false;
                    $response->message = "Not able to create Auction for this kameti by auction_id:" . $auction_id;
                }
                // Successfully create auction for this kameti
                $response->result = true;
                $response->message = "Successfully create auction for this kameti!";
            } else {
                // Failed to create Auction for this kameti
                $response->result = false;
                $response->message = "Not able to create Auction for this kameti!";
            }
        } else {
            // Kameti failed to delete
            $response->result = false;
            $response->message = "You are not the kamti admin/member. Or kameti does not exist!";
        }
        echoRespnse(200, $response);
    });



/**
 * Get All auctions for the given kameti_id. If user is the member of the kameti.
 * method GET
 * url /kameties/:id/auctions
 */

$app->get('/kameties/:kameti_id/auctions', 'authenticate', function($kameti_id) use($app) {
        global $user_id;
        global $httpdata;
        $input_data = file_get_contents("php://input");
        $httpdata = json_decode($input_data);

        $response = new stdClass();
        $item = new stdClass();

        $item->user_id = $user_id;

        // Others parameters
        $item->kameti_id = $kameti_id;                     //kameti ID

       // Creating Kameti_User Service Object
        $membersService = new membersService();

        // Let first validate the the user exists for this kamti
        $result = $membersService->isMemberExists($kameti_id, $user_id);

        if ($result) {
            // Let get All the acution for this kameti
            $auctionsService = new auctionsService();
            $auctions = $auctionsService->getAllAuction($kameti_id);

            if(is_array($auctions)){
                $response = $auctions;
            }else{
                $response->result = false;
                $response->message = "There is no auction for this kameti";
            }
        } else {
            // Failed to get the Auction list
            $response->result = false;
            $response->message = "You are not the kamti member. Or kameti does not exist!";
        }
        echoRespnse(200, $response);
    });



/**
 * Update the auction info for the given kameti_id. If user is the admin of the kameti.
 * method POST
 * url /kameties/:kameti_id/auctions/:auctions_id
 */

$app->post('/kameties/:kameti_id/auctions/:auctions_id', 'authenticate', function($kameti_id, $auction_id) use($app) {
        global $user_id;
        global $httpdata;

        $input_data = file_get_contents("php://input");
        $httpdata = json_decode($input_data);

        $response = new stdClass();
        $item = new stdClass();

        $item->user_id = $user_id;

        // reading put prams
        $item->auction_date         = isset($httpdata->auction_date) ? $httpdata->auction_date : NULL;
        $item->bid_start_time       = isset($httpdata->bid_start_time) ? $httpdata->bid_start_time : NULL;
        $item->bid_end_time         = isset($httpdata->bid_end_time) ? $httpdata->bid_end_time : NULL;

        $item->auction_winner       = isset($httpdata->auction_winner) ? $httpdata->auction_winner : 0;
        $item->auction_runnerup     = isset($httpdata->auction_runnerup) ? $httpdata->auction_runnerup : 0;


        $item->minimum_bid_amount   = isset($httpdata->minimum_bid_amount) ? $httpdata->minimum_bid_amount : 0;
        $item->maximum_bid_amount   = isset($httpdata->maximum_bid_amount) ? $httpdata->maximum_bid_amount : 0;
        $item->member_profit        = isset($httpdata->member_profit) ? $httpdata->member_profit : 0;
        $item->interest_rate        = isset($httpdata->interest_rate) ? $httpdata->interest_rate : 0;
        $item->status               = isset($httpdata->status) ? $httpdata->status : "Pending";

		
        $item->kameti_id    = $kameti_id;
        $item->user_id      = $user_id;
        $item->id           = $auction_id;

        // Creating Kameti Service Object
		$membersService = new membersService();
		
        // Let first validate the the user exists for this kamti
        $result = $membersService->isMemberExists($kameti_id, $user_id);

        if ($result) {
			// Is Auction Exists
			$auctionsService = new auctionsService();		
			$result = $auctionsService->isAuctionExists($kameti_id, $auction_id);
			
			if ($result){
                // Let now update the auction
                $result = $auctionsService->updateAuction($item);
                if($result){
                    $auction = $auctionsService->getAuctionByID($kameti_id, $auction_id);
                    $response = $auction;
                    $response->result = true;
                    $response->message = "Successfully updated  Auction!";

                    $gcmService = new gcmService();
                    $gcmService->notifyAuctionChange($item);
                } else {
                    $response->result = false;
                    $response->message = "Failed to update the Auction!";
                }
			} else {
                // Failed to update the auction as you are not the member of the kameti
                $response->result = false;
                $response->message = "Not authorize access!";
			}
			
        } else {
            // Failed to update the auction as you are not the member of the kameti
            $response->result = false;
            $response->message = "You are not the kamti admin. Or kameti does not exist!";
        }
        echoRespnse(200, $response);
    });



/**
 * Get Kameti Auction info. If user is the member of the kameti.
 * method GET
 * url /kameties/:kameti_id/auctions/:auctions_id
 */

$app->get('/kameties/:kameti_id/auctions/:auctions_id', 'authenticate', function($kameti_id, $auctions_id) use($app) {
        global $user_id;

        $response = new stdClass();
        $item = new stdClass();


        // Creating User Service Object
        $userService = new userService();

        // Creating Kameti_User Service Object
        $membersService = new membersService();

        // Let first validate the the user exists for this kamti
        $result = $membersService->isMemberExists($kameti_id, $user_id);

        if ($result) {
            $auctionsService = new auctionsService();
            $result = $auctionsService->getAuctionByID($kameti_id, $auctions_id);
            if($result != NULL){
                $response = $result;
                $response->result = true;
                $response->auction_info = $result;
            }else{
                // Failed to get the users list for the given kameti as you are not the user for this kameti
                $response->result = false;
                $response->message = "Failed to get the auctions!";
            }

        } else {
            // Failed to get the users list for the given kameti as you are not the user for this kameti
            $response->result = false;
            $response->message = "You are not the kamti user or Kameti does not exists!";
        }
        echoRespnse(200, $response);
    });


/**
 * Delete the auction of the given kameti_id. If user is the admin of the kameti.
 * method DELETE
 * url /kameties/:kameti_id/auctions/:auctions_id
 */

$app->delete('/kameties/:kameti_id/auctions/:auctions_id', 'authenticate', function($kameti_id, $auctions_id) use($app) {
        global $user_id;

        $response = new stdClass();

        // Creating Kameti Service Object
        $kametiService = new kametiService();


        //Does user is the kameti admin.
        $result = $kametiService->amIAdminOfKameti($kameti_id, $user_id);

        if ($result) {
            //Now let me delete the kameti auction as I am the admin of this kameti
            $auctionsService = new auctionsService();
            $result = $auctionsService->deleteAuction($kameti_id, $auctions_id);
            if ($result) {
                $response->result = true;
                $response->message = "Kameti auction deleted successfully!";
            }else{
                $response->result = false;
                $response->message = "Oops! Failed to delete the kameti auction";
            }
        } else {
            // Kameti failed to delete
            $response->result = false;
            $response->message = "You are not the kamti admin. Or kameti does not exist!";
        }
        echoRespnse(200, $response);
    });

/**
 * POST the bids of the given kameti_id and auction_id. If user is the member of the kameti.
 * method POST
 * url /kameties/:kameti_id/auctions/:auctions_id/bid
 */

$app->post('/kameties/:kameti_id/auctions/:auctions_id/bid', 'authenticate', function($kameti_id, $auction_id) use($app) {
        global $user_id;

        require_once '../include/bidService.php';
        $input_data = file_get_contents("php://input");
        $httpdata = json_decode($input_data);

        $response = new stdClass();
        $item = new stdClass();

        // reading put prams

        $item->bid_amount    = isset($httpdata->bid_amount) ? $httpdata->bid_amount : 0;
        $item->interest_rate  = isset($httpdata->interest_rate) ? $httpdata->interest_rate : 0;
        $item->member_id  = isset($httpdata->member_id) ? $httpdata->member_id : $user_id;

        // Creating Kameti_User Service Object
        $membersService = new membersService();

        // Let first validate the the user exists for this kamti
        $result = $membersService->isMemberExists($kameti_id, $user_id);

        if ($result) {
            $item->auction_id = $auction_id;
            $item->kameti_id  = $kameti_id;

            //Now let me delete the kameti auction as I am the admin of this kameti
            $bidService = new bidService();
            $bid_id = $bidService->createBid($item);
            if ($bid_id) {
                $item->id   = $bid_id;
                $result     = $bidService->getBidByID($item);
                $response   = $result;


                $response->result = true;
                $response->message = "Bid is created sucessfully";

                $gcmService = new gcmService();
                $gcmService->notifyBidChange($item);
            }else{
                $response->result = false;
                $response->message = "Oops! Failed to add bid";
            }
        } else {
            // Kameti failed to delete
            $response->result = false;
            $response->message = "You are not the kamti admin. Or kameti does not exist!";
        }
        echoRespnse(200, $response);
    });


/**
 * POST the bids of the given kameti_id and auction_id. If user is the member of the kameti.
 * method POST
 * url /kameties/:kameti_id/auctions/:auctions_id/bid
 */

$app->get('/kameties/:kameti_id/auctions/:auctions_id/bid', 'authenticate', function($kameti_id, $auction_id) use($app) {
        global $user_id;

        require_once '../include/bidService.php';


        $response = new stdClass();
        $item = new stdClass();


        // Creating Kameti_User Service Object
        $membersService = new membersService();

        // Let first validate the the user exists for this kameti
        $result = $membersService->isMemberExists($kameti_id, $user_id);

         if ($result) {

            //Now let me delete the kameti auction as I am the admin of this kameti
            $bidService = new bidService();
            $bids = $bidService->getAllBids($kameti_id, $auction_id);

            if(is_array($bids)){
                $response = $bids;
            }else{
                $response->result = false;
                $response->message = "There is no bid for $kameti_id : $auction_id";
            }
        } else {
            // Failed to get the Auction list
            $response->result = false;
            $response->message = "You are not the kameti member. Or kameti does not exist!";
        }
        echoRespnse(200, $response);
    });


/**
 * POST the bids of the given kameti_id and auction_id. If user is the member of the kameti.
 * method POST
 * url /kameties/:kameti_id/auctions/:auctions_id/bid
 */

$app->post('/kameties/:kameti_id/auctions/:auctions_id/bid/:bid_id', 'authenticate', function($kameti_id,$auction_id,$bid_id) use($app) {
        global $user_id;

        require_once '../include/bidService.php';


        $response = new stdClass();
        $item = new stdClass();


        // Creating Kameti_User Service Object
        $membersService = new membersService();

        // Let first validate the the user exists for this kamti
        $result = $membersService->isMemberExists($kameti_id, $user_id);

         if ($result) {
            $item->kameti_id    = $kameti_id;
            $item->auction_id   = $auction_id;
            $item->bid_id       = $bid_id;
            $item->bid_status   = "Closed";

            //Now let me delete the kameti auction as I am the admin of this kameti
            $bidService = new bidService();
            $bids = $bidService->updateBid($item);

            if($bids){
                $response->result = true;
                $response->message = "Bid is updated successfully for $item->kameti_id : $item->auction_id";

                $result = $bidService->getBidByID($item);
                $response = $result;

                $gcmService = new gcmService();
                $gcmService->notifyBidChange($result);
            }else{
                $response->result = false;
                $response->message = "There is no bid $item->kameti_id : $item->auction_id";
            }
        } else {
            // Failed to get the Auction list
            $response->result = false;
            $response->message = "You are not the kamti member. Or kameti does not exist!";
        }
        echoRespnse(200, $response);
    });

$app->get('/session', function() {
    $session = getSession();
    $response["id"] = $session['id'];
    $response["mobile"] = $session['mobile'];
    $response["name"] = $session['name'];
    $response["device_key"] = isset($session['device_key']) ? $session['device_key'] : "ABCD";
    echoRespnse(200, $response);
});

$app->get('/logout', function() {
    $session = destroySession();
    $response["status"] = "info";
    $response["message"] = "Logged out successfully";
    echoRespnse(200, $response);
});

$app->run();
?>
