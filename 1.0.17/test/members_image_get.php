<?php

$image_file = 'c:/Adobe/download.png';
    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: b10b4daff3c88eaa',
                        'mobile: 9891533910'
                    );

    $url = "http://192.168.43.150/kameti/zojila/v1/members/image";

    $fp = fopen ($image_file, 'w+');
    $handle = curl_init();

    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_FILE, $fp);          // output to file
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($handle, CURLOPT_TIMEOUT, 1000);      // some large value to allow curl to run for a long time
    curl_setopt($handle, CURLOPT_USERAGENT, 'Mozilla/5.0');

    curl_setopt($handle, CURLOPT_HTTPGET, true);
    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'GET');

    $response = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

    fclose($fp);
    return $response;

?>
