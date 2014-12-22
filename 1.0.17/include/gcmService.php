<?php

/**

 *
 * @author Pramod Kumar Raghav
 *
 */
class gcmService {

	public function notifyPofileImageChange($member_id) {

		$item = new stdClass();
		$registatoin_ids = array();

		$item->user_id = $member_id;

		// fetching all user tasks
        $kametiService = new kametiService();
		$membersService = new membersService();
		$deviceService = new deviceService();
		$gcm = new GCM();// Send push notification for android mobile


		// Get the Kameti list of the given member
        $kametiList = $kametiService->getUserAllKameties($item->user_id);

		if($kametiList){
			// Let now get members list of kameties
			$membersList = $membersService->getKametiAllMembers($kametiList);

			for ($km=0; $km<=count($membersList) - 1; $km++) {
				//TODO
				$kametiMemberObj = $membersList[$km];

				$device_key = $deviceService->getDeviceKeyByUserID($kametiMemberObj->member_id);

				if ($device_key != null){
					$registatoin_ids[] = $device_key;
				}
			}

			$message = array("table" => "users_image",
							 "message" => "Profile Image changed for member ID: $member_id",
							 "member_id" => $member_id);
			$result = $gcm->send_notification($registatoin_ids, $message);
		}
	}

	public function notifyAuctionChange($item){
		$registatoin_ids = array();
		$item->user_id;

		// fetching all user tasks

		$membersService = new membersService();
		$deviceService = new deviceService();
		$gcm = new GCM();// Send push notification for android mobile

		// Let now get members list of kameties
		$membersList = $membersService->getAllMembers($item->kameti_id);

		for ($km=0; $km<=count($membersList) - 1; $km++) {
			//TODO
			$kametiMemberObj = $membersList[$km];

			$device_key = $deviceService->getDeviceKeyByUserID($kametiMemberObj->member_id);

			if ($device_key != null){
				$registatoin_ids[] = $device_key;
			}
		}

		$message = array("table" => "auction",
						 "message" => "Auction update available for kameti ID: $item->kameti_id",
						 "kameti_id" => $item->kameti_id);
		$result = $gcm->send_notification($registatoin_ids, $message);

	}


	public function notifyBidChange($item){
		$registatoin_ids = array();
		$item->user_id =  $item->member_id;

		// fetching all user tasks
        $kametiService = new kametiService();
		$membersService = new membersService();
		$deviceService = new deviceService();
		$gcm = new GCM();// Send push notification for android mobile


		// Let now get members list of kameties
		$membersList = $membersService->getAllMembers($item->kameti_id);

		for ($km=0; $km<=count($membersList) - 1; $km++) {
			//TODO
			$kametiMemberObj = $membersList[$km];

			$device_key = $deviceService->getDeviceKeyByUserID($kametiMemberObj->member_id);

			if ($device_key != null){
				$registatoin_ids[] = $device_key;
			}
		}

		$message = array("table" => "bid",
						  "message" => "Got a new bid for Kameti ID: $item->kameti_id",
							"auction_id" => $item->auction_id,
							"kameti_id" => $item->kameti_id);
		$result = $gcm->send_notification($registatoin_ids, $message);

	}
}

?>
