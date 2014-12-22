<?php

require_once '../include/kametiService.php';
require_once '../include/membersService.php';
require_once '../include/deviceService.php';
require_once '../include/bidService.php';
require_once '../include/gcmService.php';
require_once '../include/GCM.php';

$user_id = 1;
$auction_id = 3;
$kameti_id = 1;


$response = new stdClass();
 $item = new stdClass();

 // reading put prams

 $item->bid_amount    = isset($httpdata->bid_amount) ? $httpdata->bid_amount : 2352;
 $item->interest_rate  = isset($httpdata->interest_rate) ? $httpdata->interest_rate : 1.4;
 $item->member_id  = isset($httpdata->member_id) ? $httpdata->member_id : 1;

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
         $item->id = $bid_id;
         $result = $bidService->getBidByID($item);

         $response = $result;


         $response->result = true;
         $response->message = "Bid is created sucessfully";

         $gcmService = new gcmService();
         $gcmService->notifyBidChange($result);
     }else{
         $response->result = false;
         $response->message = "Oops! Failed to add bid";
     }
 } else {
     // Kameti failed to delete
     $response->result = false;
     $response->message = "You are not the kamti admin. Or kameti does not exist!";
 }


?>
