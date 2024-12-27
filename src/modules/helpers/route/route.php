<?php

namespace Modules\Helpers\Route;

class Route
{
    private $route;
    private $controller;
    private $action;
    private $method;
    private $middlewares = [];

    private function __construct($route, $controllerPath, $action, $method)
    {
        $this->route = $route;
        $this->controller = $controllerPath;
        $this->action = $action;
        $this->method = $method;
    }

    public function middleware(array $middlewares)
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public function returnRoute()
    {
        return [
            'route' => $this->route,
            'controller' => $this->controller,
            'action' => $this->action,
            'method' => $this->method,
            'middlewares' => $this->middlewares
        ];
    }

    public static function createRoute($route, $controllerPath, $action, $method)
    {
        return new self($route, $controllerPath, $action, $method);
    }

    public static function GET($route, $controllerPath, $action)
    {
        return self::createRoute($route, $controllerPath, $action, 'GET');
    }

    public static function POST($route, $controllerPath, $action)
    {
        return self::createRoute($route, $controllerPath, $action, 'POST');
    }

    public static function PUT($route, $controllerPath, $action)
    {
        return self::createRoute($route, $controllerPath, $action, 'PUT');
    }

    public static function DELETE($route, $controllerPath, $action)
    {
        return self::createRoute($route, $controllerPath, $action, 'DELETE');
    }

    // Getter methods for route, controller, action, method, and middleware
    public function getRoute()
    {
        return $this->route;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getMiddleware()
    {
        return $this->middlewares;
    }
}
