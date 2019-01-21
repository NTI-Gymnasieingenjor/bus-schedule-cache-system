<?php

$lines = explode("\n", file_get_contents('gtfs/stops.txt'));

function newStop($values)
{
    return [
        'id' => $values[0],
        'name' => $values[1],
        'times' => [],
    ];
}

function shouldKeep($name)
{
    $keep = [];
    foreach ($keep as $k) {
        if ($k == $name) {
            return true;
        }
    }
    return false;
}

$stops = [];

$index = 1;
while ($index < count($lines)) {
    $line = $lines[$index];
    $values = explode(',', $line);
    $data[$values[0]] = newStop($values);
    $index++;
}

print_r($data);

// Solve by inserting all data into SQL database
