<?php

    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: APA91bGMA2fL1-mPNws--hkvOb9aMDtAeh84Asx0Fv8ndOzli5gIfH75fficUD65vNqaCGbs6mUsUO7uwADt76SviHpesI2ZEVXpR8-_fblEcQn_dx9w53RPrJ1YC6L-mBImARo4rRMvbpG7m6giYDx1ulIlMyAuDQ',
                        'mobile: 9891533910'
                    );


    $url = "http://only4ladies.in/kameti/v1/kameties/1/auctions/1/bid";

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
