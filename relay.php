<?php
require 'util.php';
require 'cache.php';

$start = microtime(true);

// Get URL
$url = Util::input('url');

// Cache url response if not already cached
if (!Cache::exists($url)) {
    $data = Util::fetch($url);
    Cache::clear($url);
    Cache::create($url, $data);

    Util::setHeader('Response-Source', 'Request');
} else {
    Util::setHeader('Response-Source', 'Cache');
}

// Fetch response
$data = Cache::fetch($url);
if (!$data) {
    $json = Util::json(json_encode(['message' => 'Realtime data offline.', 'description' => 'See offline cache.']));
    return Util::jsonResponse($payload);
}
$json = Util::json($data);
$payload = Util::json($json->Payload);

//$payload->executionTime = round((microtime(true) - $start) * 1000 * 1000);

return Util::jsonResponse($payload);
