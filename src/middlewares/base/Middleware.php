<?php

namespace Middlewares\Base;

class Middleware
{
    protected $autorized;

    public function __construct($autorized)
    {
        $this->autorized = $autorized;
    }

    public static function initMiddlewares($middlewareArray, $request)
    {
        $canPass = true;
        if (!empty($middlewareArray)) {
            foreach ($middlewareArray as $middlewareName) {
                $middlewareClass = 'Middlewares\\' . $middlewareName;
                $middleware = new $middlewareClass($request);
                $autorized = $middleware->getAutorized();
                if ($autorized && !is_bool($autorized) || !$autorized) {
                    $canPass = $autorized;
                    break;
                }
            }
        }
        return $canPass;
    }

    public function getAutorized()
    {
        return $this->autorized;
    }

    public function setAutorized($autorized)
    {
        return $this->autorized = $autorized;
    }
}
