<?php

namespace Modules\Helpers\Manager\Errors;

use Modules\Helpers\Manager\Response\HttpResponse;

class httperrors
{
    public static function code404($message = 'Not Found')
    {
        header("HTTP/1.0 404 Not Found");
        header('Content-Type: application/json');
        if (is_string($message) || empty($message)) {
            $message = empty($message) ? 'Not Found' : $message;
            return HttpResponse::JSON(['error' => "$message"]);
        }
        return HttpResponse::JSON($message);
    }

    public static function code500($message = 'Internal Server Error')
    {
        header("HTTP/1.1 500 Internal Server Error");
        header('Content-Type: application/json');
        if (is_string($message) || empty($message)) {
            $message = empty($message) ? 'Internal Server Error' : $message;
            return HttpResponse::JSON(['error' => "$message"]);
        }
        return HttpResponse::JSON($message);
    }

    public static function code422($message = 'Invalid data submitted')
    {
        header("HTTP/1.1 422 Unprocessable Entity");
        header('Content-Type: application/json');
        if (is_string($message) || empty($message)) {
            $message = empty($message) ? 'Invalid data submitted' : $message;
            return HttpResponse::JSON(['error' => "$message"]);
        }
        return HttpResponse::JSON($message);
    }

    public static function code400($message = 'Bad Request')
    {
        header("HTTP/1.1 400 Bad Request");
        header('Content-Type: application/json');
        if (is_string($message) || empty($message)) {
            $message = empty($message) ? 'Bad Request' : $message;
            return HttpResponse::JSON(['error' => "$message"]);
        }
        return HttpResponse::JSON($message);
    }

    public static function code401($message = 'Unauthorized')
    {
        header("HTTP/1.1 401 Unauthorized");
        header('Content-Type: application/json');
        if (is_string($message) || empty($message)) {
            $newMessage = empty($message) ? 'Unauthorized' : $message;
            return HttpResponse::JSON(['error' => "$newMessage"]);
        }
        return HttpResponse::JSON($message);
    }
}
