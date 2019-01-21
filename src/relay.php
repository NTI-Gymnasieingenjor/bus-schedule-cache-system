<?php
require 'util.php';
require 'cache.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST,GET,OPTIONS');
header('Access-Control-Allow-Headers: *');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$start = microtime(true);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
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
if (!$data) {
    $payload = Util::json(json_encode(['message' => 'Realtime data offline', 'description' => 'See offline cache.']));
    return Util::jsonResponse($payload);
}
$json = Util::json($data);
$payload = Util::json($json->Payload);

return Util::jsonResponse($payload);
