<?php

include_once '../include/gcmService.php';

$gcmService = new gcmService();
$member_id = 1;
$gcmService->notifyPofileImageChange($member_id);


?>
