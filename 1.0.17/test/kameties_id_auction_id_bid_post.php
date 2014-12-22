<?php

    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: APA91bE-CE2J6TROYLy3yBJkoBZrpEFiqHo6dPkQgdDDmE93O73YN--byl8KR_g492SmU-zC5lz_D1dc4UFOZ3qWoLhLV4s4xTUsBS36VfhQHpe5diQroMc2Kf2lAGXXSLl-hXY7Tz8jEuKMmAzS6Ut2XxCsewABBQ',
                        'mobile: 9953765780'
                    );

    $vars = array(
                   'bid_amount' => "2351",
                   'interest_rate' => "1.35"
                );

    $data = json_encode( $vars );
    $url = "http://only4ladies.in/kameti/v1/kameties/9/auctions/16/bid";
    #$url = "http://localhost/kameti/zojila/v1/kameties/1/auctions/1/bid";


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
