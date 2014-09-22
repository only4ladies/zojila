<?php

function getConfigInfo(){
	$xml = simplexml_load_file("assets/db_config.xml");
	return $xml;
}
?>
