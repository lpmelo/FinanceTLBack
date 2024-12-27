<?php

namespace Config;

use Config\HttpHandler;

class Application
{
    private $handler;
    public function __construct()
    {
        $this->handler = new HttpHandler();
    }

    public function initApplication()
    {
        while (true) {
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                Cors::corsOptions();
                exit;
            }

            if ($_SERVER['REQUEST_METHOD']) {
                $this->handler->handleRequest();
                exit;
            }

            sleep(1);
        }
    }
}
