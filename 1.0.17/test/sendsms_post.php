<?php

    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                    );

    $vars = array(
                    "mobile" => '9891533910',
                    "message" => 'SMS code for KAMETI is : 1234'
                );

    $data = json_encode( $vars );
    $url = "http://localhost/kameti/zojila/v1/sendsms";

    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'POST');

    $response = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    return $response;

?>
