<?php

    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                    );
    
    $vars = array(
                    "mobile_email" => 9891533910,
                    "verification_code" => 1941,
                );
    
    $data = json_encode( $vars );
    $url = "http://localhost/kameti/rest/member/userVerify";
    
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
    
    $response = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    return $response;

?>