<?php

require 'util.php';
require 'cache.php';
require 'cors.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$start = microtime(true);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(200);
    $payload = Util::json(json_encode(['message' => 'Bad method', 'description' => 'Please send a POST request instead.']));

    return Util::jsonResponse($payload);
}

// Get URL
$url = Util::input('url');

// Cache url response if not already cached
if (!Cache::exists($url)) {
    $data = Util::fetch($url);
    //Cache::clear($url);
    Cache::create($url, $data);

    Util::setHeader('Cobalt-Lama-Response-Source', 'request');
} else {
    Util::setHeader('Cobalt-Lama-Response-Source', 'cache');
}

// Fetch response
// Fetch response
$data = Cache::fetch($url);
if (!$data || !isset(Util::json($data)->Payload)) {
    http_response_code(503);
    $payload = Util::json(json_encode(['message' => 'Realtime data offline', 'description' => 'See offline cache.']));
    return Util::jsonResponse($payload);
}
$json = Util::json($data);
$payload = Util::json($json->Payload);
http_response_code(200);
return Util::jsonResponse($payload);
