<?php

function getConfigInfo(){
	$xml = simplexml_load_file("assets/Config/configurations.xml");
	return $xml;
}
?>
