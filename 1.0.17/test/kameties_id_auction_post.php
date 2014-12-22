<?php

    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: APA91bGQGHKFOzcIDduPjYLUzFsM1NNfFQZom4dOEeDBzXoDnVmbEObqKzKTNYR2IAtfVITDZ9r5sQ6hcWraZzAWWGgAJ28n6o2ipOcZMkto2qNQAjbXWvJ8PucnOUQBrAXJ2lXL-9QO5teoahsYaLKM9osOee18rQ',
                        'mobile: 9891533910'
                    );

    $vars = array(
                   'kameti_id' => "1",
                   'auction_date' => "2014-11-14",
                   'bid_start_time' => "14:30:00",
                   'bid_end_time' => "16:30:00",
                   'auction_winner' => 0,
                   'auction_runnerup' => 0,
                   'minimum_bid_amount' => 2250,
                   'maximum_bid_amount' => 0,
                   'member_profit' => 0,
                   'interest_rate' => 1.0,
                   'status' => "Pending"
                );

    $data = json_encode( $vars );
    $url = "http://localhost/kameti/zojila/v1/kameties/1/auctions";

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
