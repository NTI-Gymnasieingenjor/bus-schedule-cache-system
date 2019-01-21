<?php

// Preform a basic GET request using cURL
class Util
{
    public static function fetch($url)
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

    public static function input($key)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        return $data[$key];
    }

    public static function json($data)
    {
        return json_decode($data);
    }

    public static function setHeader($key, $value): void
    {
        header($key . ": " . $value);
    }

    public static function jsonResponse($data)
    {
        self::setHeader('Content-Type', 'application/json');

        echo json_encode($data);
        exit;
    }
}
