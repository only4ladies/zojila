<?php

    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: APA91bFcNVlk_TR8jeXq_Pq1MJd_JckpO8TbV94EJIXaXVADea4zvOD4Xqd8sKk2c3ZHsAXyKJSKXS--jRzouCYU8dBnc6N_a_W-TvbywNcU2aKFpQ-aObcKl9JcfGGn8OastKhIabjDAyVkC5yri8WSBh2s86zmpA',
                        'mobile: 9891533917'
                    );

    $url = "http://kameti.only4ladies.com/1.0.17/v1/login";

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
