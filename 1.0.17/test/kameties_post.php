<?php

    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: APA91bEz85cP4oOLCxIGsPIZcqtSisGNvrUXDfK-rMy5buVvTdX_oVSsAZF8-2g-emTm_Fk47N3hEhYd2XsuxRORI4BWycUBb6muHtJBFQb4_yCHZkhEOFIxtGedpBzuRa827ow0QB0xKt6HLSuz2gkz4KreGaq35Q',
                        'mobile: 9891533930'
                    );

    $vars = array(
                   'kameti_name' => "Mamta Raghav",
                   'kameti_start_date' => "2014-11-14",
                   'kameti_members' => 20,
                   'kameti_amount' => 50000,
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
    $url = "http://kameti.only4ladies.com/v1/kameties";

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
