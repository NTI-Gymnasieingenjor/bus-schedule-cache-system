<?php

if (file_exists('info.json')) {
    $info = json_decode(file_get_contents('info.json'));
    if ($info->lastUpdate < date('Y-m-d')) {
        $update = true;
    } else {
        $update = false;
    }
} else {
    $update = true;
}

if ($update) {
    $keep = [["start" => "Uppsala Business Park Norra", "stop" => "Uppsala Centralstation"], ["start" => "Uppsala Södra Slavstavägen", "stop" => "Uppsala Centralstation"], ["start" => "Uppsala Södra Slavstavägen", "stop" => "Storvreta Centrum (Uppsala kn)"]];

    $days = 7;

    function get($url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36',
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

// Get stops
    $fetched_stops1 = json_decode(get('https://api.resrobot.se/v2/location.nearbystops?key=ba6be5bd-4d43-4b77-b250-b28867fcf1e1&originCoordLat=59.8558536&originCoordLong=17.7080297&r=10000&maxNo=10&format=json'));
    $fetched_stops2 = json_decode(get('https://api.resrobot.se/v2/location.nearbystops?key=ba6be5bd-4d43-4b77-b250-b28867fcf1e1&originCoordLat=59.8332051&originCoordLong=17.518366&r=10000&maxNo=1000&format=json'));
    $fetched_stops3 = json_decode(get('https://api.resrobot.se/v2/location.nearbystops?key=ba6be5bd-4d43-4b77-b250-b28867fcf1e1&originCoordLat=59.9594701&originCoordLong=17.7059115&r=10000&maxNo=1000&format=json'));
    $fetched_stops = array_merge($fetched_stops1->StopLocation, $fetched_stops2->StopLocation, $fetched_stops3->StopLocation);
    $stops = [];

    foreach ($fetched_stops as $fs) {
        $index = 0;
        foreach ($keep as $ks) {
            if ($ks['start'] == $fs->name) {
                $keep[$index]['start'] = $fs;
            }
            if ($ks['stop'] == $fs->name) {
                $keep[$index]['stop'] = $fs;
            }
            $index++;
        }
    }

// Fetch time tables
    foreach ($keep as $s) {
        //echo 'Processing ' . $s['start']->name . ' –> ' . $s['stop']->name . "...\n";
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $i . ' days'));
            $timetable = json_decode(get('https://api.resrobot.se/v2/departureBoard?key=59d2a4ac-a8aa-4ea8-a7a6-ef1e34e25ecc&id=' . $s['start']->id . '&direction=' . $s['stop']->id . '&maxJourneys=10000&passlist=0&date=' . $date . '&format=json'));
            //echo 'https://api.resrobot.se/v2/departureBoard?key=59d2a4ac-a8aa-4ea8-a7a6-ef1e34e25ecc&id=' . $s['start']->id . '&direction=' . $s['stop']->id . '&maxJourneys=10000&passlist=0&date=' . $date . '&format=json' . "\n";
            $lines = new \stdClass();
            if (isset($timetable->Departure)) {
                foreach ($timetable->Departure as $departure) {
                    $line = $departure->transportNumber;
                    $lines->$line = [];
                }
                foreach ($timetable->Departure as $departure) {
                    $line = $departure->transportNumber;
                    array_push($lines->$line, $departure);
                }
                foreach ($lines as $key => $value) {
                    file_put_contents('offline-cache/' . $date . '-' . $key . '.json', json_encode($value));
                }
            }
        }
    }
    echo "Updated offline cache.";
} else {
    echo "Already up to date.";
}

$info = new \stdClass();
$info->lastUpdate = date('Y-m-d');
file_put_contents('info.json', json_encode($info));
