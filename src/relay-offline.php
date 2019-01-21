<?php

require 'util.php';
require 'cors.php';
require 'config.php';

$results = 5;
$line = Util::input('line');
if (!$line) {
    $payload = Util::json(json_encode(['message' => 'Unknown transit line', 'description' => 'Please provide a transit line']));
    return Util::jsonResponse($payload);
}

$response = [];

$fn = 'offline-cache/' . date('Y-m-d') . '-' . $line . '.json';

if (file_exists($fn)) {
    $data = json_decode(file_get_contents($fn));

    $now = new DateTime('1 minute ago');

    foreach ($data as $departure) {
        if ($now < new DateTime($departure->time)) {
            array_push($response, $departure);
        }
        if (count($response) >= $results) {
            break;
        }
    }
} else {
    $reponse = json_encode(['message' => 'Unable to find offline files.']);
}

$payload = Util::json(json_encode($response));

return Util::jsonResponse($payload);
