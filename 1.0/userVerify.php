<?php

	require_once("dbConnection.php");
	#load cbs module
	require_once("RESTServices/userVerifyService.php");
	
	$userVerifyService = new userVerifyService();
	
	#initializing and validating global JSON variables 
	$input_data = file_get_contents('php://input');
	if($input_data){
		
		$item = json_decode($input_data);
		
		#depending on request method, call the respective service
		if($_SERVER['REQUEST_METHOD'] == 'PUT'){
			$item =  $userVerifyService->createUserVerification($item);
			$result =  $userVerifyService->sendVerificationCode($item);
			
		}elseif($_SERVER['REQUEST_METHOD'] == 'DELETE'){
			$result =  $userVerifyService->getUserVerification($item);
		}
		
		echo json_encode($result);
	}

?>