<?php
    $headers = array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: APA91bGe47IeYu5wP6EaBwC6wQY8R1Zv-SmBbGIIh0Nr0NR-PTaRupmHdU9NUzUb8n6h_54hO0Cctr2-gu5Cq0jROAAnVJsLITPFKNVLYZIfEGi0JlHPTl6jGySTx4VJST8I4n5EyapsxV6WAdkJ83ysaVCrWVpB2Q',
                        'mobile: 9891533910'
                    );


    $url = "http://10.40.245.49/kameti/zojila/v1/members/image/1";

    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'POST');


    $response = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    return $response;

?>
