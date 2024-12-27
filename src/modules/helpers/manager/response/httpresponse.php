<?php

namespace Modules\Helpers\Manager\Response;

class HttpResponse
{
    public static function JSON($object)
    {
        header('Content-Type: application/json');
        echo json_encode($object);
    }
}
