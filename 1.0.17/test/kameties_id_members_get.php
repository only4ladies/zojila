<?php

    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: b10b4daff3c88eaa',
                        'mobile: 9891533910'
                    );



    $url = "http://localhost/kameti/zojila/v1/kameties/1/members";

    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($handle, CURLOPT_HTTPGET, true);
    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'GET');

    $response = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    return $response;


?>
