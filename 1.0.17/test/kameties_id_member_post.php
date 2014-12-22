<?php


    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: APA91bGJkHc2wl62Qp1DzObC47yRwlD-Z-4sr6yy8yFJnf6kcNIhqH8yJnDCuBorEcqHzSLlQDng8NVezBHHZ4064JP8zI4JV2SGhgETtSq0E5iiV-745gGaSV16hTQ0u7nASZswrXoOHaojP5XF43356B5qkd4LjA',
                        'mobile: 8130358435'
                    );

    $vars = array(
                   'name' => "Pramod Raghav",
                   'mobile' => "9891533930"
                );

    $data = json_encode( $vars );
    #$url = "http://only4ladies.in/kameti/v1/kameties/1/members";
    $url = "http://localhost/kameti/zojila/v1/kameties/1/members";

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
