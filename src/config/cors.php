<?php

namespace Config;

class Cors
{
    public static function corsOptions()
    {
        $origin = $_ENV['CROSS_ORIGIN_ACCEPTED_URL'];
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Credentials: false");
        header("Content-Length: 0");
        header("Content-Type: text/plain");
    }

    public static function initCors($corsObject)
    {
        $corsKey = 'origin';
        $methodsKey = 'methods';
        $headersAllowKey = 'headers.allow';
        $headersExposeKey = "headers.expose";
        $credentialsKey = "credentials";

        header("Access-Control-Allow-Origin:$corsObject[$corsKey]");
        header("Access-Control-Allow-Methods:$corsObject[$methodsKey]");
        header("Access-Control-Allow-Headers:$corsObject[$headersAllowKey]");
        header("Access-Control-Expose-Headers:$corsObject[$headersExposeKey]");
        header("Access-Control-Allow-Credentials:$corsObject[$credentialsKey]");
    }
}
