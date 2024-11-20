<?php

namespace Modules\Helpers\Manager\Response;

class httpresponse
{
    public static function JSON($object)
    {
        header('Content-Type: application/json');
        echo json_encode($object);
    }
}
