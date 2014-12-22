<?php

    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: APA91bGMA2fL1-mPNws--hkvOb9aMDtAeh84Asx0Fv8ndOzli5gIfH75fficUD65vNqaCGbs6mUsUO7uwADt76SviHpesI2ZEVXpR8-_fblEcQn_dx9w53RPrJ1YC6L-mBImARo4rRMvbpG7m6giYDx1ulIlMyAuDQ',
                        'mobile: 9891533910'
                    );

    $vars = array(
                   "auction_date" =>"2014-11-15",
                   "maximum_bid_amount" =>0,
                   "auction_runnerup" => 0,
                   "kameti_id" => 1,
                   "auction_winner" => 0,
                   "member_profit" => 0,
                   "minimum_bid_amount" => 2250,
                   "bid_end_time" => "14:00:00",
                   "bid_start_time" => "15:00:00"
                );

    $data = json_encode( $vars );
    $url = "http://only4ladies.in/kameti/v1/kameties/1/auctions/1";

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
