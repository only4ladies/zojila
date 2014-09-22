<?php

	include "dbConnection.php";
	#load cbs module
	include "RESTServices/kametiService.php";
	include "RESTServices/userProfileService.php";
	include "RESTServices/kametiMemberService.php";
	
	$kametiService = new kametiService();
	$userProfileService = new userProfileService();
	$kametiMemberService = new kametiMemberService();
	
	#initializing and validating global JSON variables 
	$input_data = file_get_contents('php://input');
	if($input_data){

		$item = json_decode($input_data);

		#depending on request method, call the respective service
		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			$item =  $kametiService->getKameti($item);
		}elseif($_SERVER['REQUEST_METHOD'] == 'PUT'){
			$item =  $kametiService->updateKameti($item);
		}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){

			$item = $userProfileService->getUserID($item);
			$item =  $kametiService->createKameti($item);

			/* 
			 * Kameti has been created sucessfully 
			 * Let now Add the member for this kameti
			 *
			 */
			$userProfile  = new stdClass();
			$userProfile->kameti_id = $item->kameti_id;

			foreach ($item->members as $mobile_number => $user_name){
				$userProfile->mobile_number = "$mobile_number"; // Do not remove \""\" other wise mobile number might be treated as number and MySQL query will not work
				$userProfile->user_name = "$user_name";
				
				/*
				 * Check whether member already exists
				 */


				$userProfile = $userProfileService->getUserProfile($userProfile);
				if (isset($userProfile->member_id)) {
					// Add new member as given mobile does not exits in our database
					$userProfile = $userProfileService->createUserProfile($userProfile);
				}
				
				/*
				 * Let add the member in a kameti_member list
				 */

				$kametiMemberm = $kametiMemberService->createKametiMember($userProfile);
			}
		}elseif($_SERVER['REQUEST_METHOD'] == 'DELETE'){
			$result =  $kametiService->deleateKameti($item);
		}
		
		echo json_encode($result);
	}

?>