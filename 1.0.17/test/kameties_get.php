<?php

    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: APA91bHNnLTm6RKQFx38aE39-8qCGVSnBLKyAuVzjFsBeDACFMXO-V0zFWrfEiaSxHupJT5uFuAhmUpUqtJdir9214UO31i-23u9HsNwbv6vm2wD9rIoqMq6Z5kC9X9A86BLLBGRb2Od2vw2--siAZ7nqsdadSSZJg',
                        'mobile: 9891533910'
                    );

    $url = "http://localhost/kameti/zojila/v1/kameties";

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
