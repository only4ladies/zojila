<?php

    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                    );
    
    $vars = array(
                    "kamet_name" => 'ABCD',
                    "kameti_start_date" => "2014-09-19",
                    "kameti_members" => 10,
                    "kameti_amount" => 25000,
                    "kameti_intrest_rate" => 1.5,
                    "bid_start_time" => "13:30:00",
                    "bid_end_time" => "15:00:00",
                    "bid_amout_minimum" => 100,
                    "bid_timer" => 5,
                    "lucky_draw_amount" => 200,
                    "lucky_members" => 2,
                    "runnerup_percentage" => 50,
                    "kameti_rule" => 1,
                    "mobile_number" => '9891533910'
                );
    
    $data = json_encode( $vars );
    $url = "http://localhost/kameti/rest/member/kameti";
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'GET');
    
    $response = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    return $response;

?>