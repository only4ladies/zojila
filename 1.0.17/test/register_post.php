<?php

    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: APA91bEz85cP4oOLCxIGsPIZcqtSisGNvrUXDfK-rMy5buVvTdX_oVSsAZF8-2g-emTm_Fk47N3hEhYd2XsuxRORI4BWycUBb6muHtJBFQb4_yCHZkhEOFIxtGedpBzuRa827ow0QB0xKt6HLSuz2gkz4KreGaq35Q',

                    );

    $vars = array(
                    "mobile" => '9891533930',
                    "name"  => 'Pramod Raghav',
                    "developer_payload"  => 'f3ec49e9fadbba1e9891533930'
                );

    $data = json_encode( $vars );
    $url = "http://kameti.only4ladies.com/v1/register";

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
