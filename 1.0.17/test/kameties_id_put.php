<?php

    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: 12345lsrnd9pram123',
                        'mobile: 98918221301'
                    );

    $vars = array(
                   'kameti_name' => "Mamta Rani",
                   'kameti_start_date' => "2014-09-10",
                   'kameti_members' => 10,
                   'kameti_amount' => 25000,
                   'kameti_interest_rate' => 1.0,
                   'bid_start_time' => "14:00:00",
                   'bid_end_time' => "16:00:00",
                   'bid_amount_minimum' => 50,
                   'bid_timer' => 5,
                   'lucky_draw_amount' => 0,
                   'lucky_members' => 0,
                   'runnerup_percentage' => 0,
                   'kameti_rule' => 1
                );

    $data = json_encode( $vars );
    $url = "http://localhost/kameti/zojila/v1/kameties/8";

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
