<?php


// Copyright 2019 Oath Inc. Licensed under the terms of the zLib license see https://opensource.org/licenses/Zlib for terms.
function buildBaseString($baseURI, $method, $params) {
    $r = array();
    ksort($params);
    foreach($params as $key => $value) {
        $r[] = "$key=" . rawurlencode($value);
    }
    return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
}
function buildAuthorizationHeader($oauth) {
    $r = 'Authorization: OAuth ';
    $values = array();
    foreach($oauth as $key=>$value) {
        $values[] = "$key=\"" . rawurlencode($value) . "\"";
    }
    $r .= implode(', ', $values);
    return $r;
}




function fetchWeather() {
    $url = 'https://weather-ydn-yql.media.yahoo.com/forecastrss';
    $app_id = 'eYe8iS64';
    $consumer_key = 'dj0yJmk9NTNqYXpMRHNqZXNaJnM9Y29uc3VtZXJzZWNyZXQmc3Y9MCZ4PWRh';
    $consumer_secret = '26d4a8d6f3487049706b9105f1256195d7436379';
    $query = array(
        'location' => 'london,uk',
        'format' => 'json',
        'u' => 'c'
    );
    $oauth = array(
        'oauth_consumer_key' => $consumer_key,
        'oauth_nonce' => uniqid(mt_rand(1, 1000)),
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp' => time(),
        'oauth_version' => '1.0'
    );
    $base_info = buildBaseString($url, 'GET', array_merge($query, $oauth));
    $composite_key = rawurlencode($consumer_secret) . '&';
    $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
    $oauth['oauth_signature'] = $oauth_signature;
    $header = array(
        buildAuthorizationHeader($oauth),
        'X-Yahoo-App-Id: ' . $app_id
    );
    $options = array(
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_HEADER => false,
        CURLOPT_URL => $url . '?' . http_build_query($query),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false
    );
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    curl_close($ch);
    file_put_contents("cache/lonweather.json", $response);
}


function printWeather() {
  $response = file_get_contents("cache/lonweather.json");
  header('Content-Type: application/json');
  header('Access-Control-Allow-Origin: *');
  # callback
  if (isset($_REQUEST['callback'])) {
    print($_REQUEST['callback'] . "($response);");
  } else {
    print($response);
  }

}


##
## 
if (file_exists("cache/lonweather.json") && time()-filemtime("cache/lonweather.json") < 300) {
  printWeather();
} else {
  fetchWeather();
  printWeather();
}




?>
