<?php

	require_once("dbConnection.php");
	#load cbs module
	require_once("RESTServices/userProfileService.php");
	
	$userProfileService = new userProfileService();
	
	#initializing and validating global JSON variables 
	$input_data = file_get_contents('php://input');
	if($input_data){
		
		$item = json_decode($input_data);
		
		#depending on request method, call the respective service
		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			$result =  $userProfileService->getUserProfile($item);	
		}elseif($_SERVER['REQUEST_METHOD'] == 'PUT'){
			$result =  $userProfileService->updateUserProfile($item);	
		}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
			$result =  $userProfileService->createUserProfile($item);	
		}elseif($_SERVER['REQUEST_METHOD'] == 'DELETE'){
			$result =  $userProfileService->deleateUserProfile($item);
		}
		
		echo json_encode($result);
	}

?>