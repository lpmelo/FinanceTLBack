<?php

namespace Config;

use Modules\Helpers\Manager\Errors\HttpErrors;
use Routes\Router;
use Middlewares\Base\Middleware;

class HttpHandler
{
    public function handleRequest()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Ignora os parâmetros de consulta na URI
        $method = $_SERVER['REQUEST_METHOD'];
        $headers = getallheaders();
        $body = $this->threatBody(file_get_contents("php://input"));

        // Caso for um GET, resgata os parâmetros a partir da URL
        if (!empty($_GET)) {
            $valores = array_keys($_GET);
            if(!empty($valores)){
                $json = $valores[0];
                $body = $this->threatBody($json);
            }
        }

        $routes = Router::init();
        $matchedRoute = $this->matchRoute($uri, $method, $routes, $headers, $body);

        if (is_array($matchedRoute) && !empty($matchedRoute)) {
            $params = $matchedRoute['params'];
            $controllerClass = $matchedRoute['controller'];
            $action = $matchedRoute['action'];
            $func_args = ['params' => $params, 'headers' => $headers, 'data' => $body];
            $controller = new $controllerClass();
            call_user_func([$controller, $action], $func_args);
        } else {
            if (!empty($matchedRoute) && !is_bool($matchedRoute)) {
                return HttpErrors::code401($matchedRoute);
            }
            return HttpErrors::code404();
        }
    }

    private function matchRoute($uri, $method, $routes, $headers, $body)
    {
        foreach ($routes as $routeInstance) {
            $route = $routeInstance->returnRoute();
            if ($this->isMethodMatch($method, $route['method']) && $this->isUriMatch($uri, $route['route'])) {
                $params = $this->extractParams($uri, $route['route']);
                $request = ['params' => $params, 'headers' => $headers, 'data' => $body];
                $middleware = Middleware::initMiddlewares($route['middlewares'], $request);
                
                if (!$middleware || !is_bool($middleware)) {
                    return $middleware;
                }
                $route['params'] = $params;
                return $route;
            }
        }
        return null;
    }

    private function isMethodMatch($requestedMethod, $allowedMethod)
    {
        return strtoupper($requestedMethod) === strtoupper($allowedMethod);
    }

    private function isUriMatch($requestedUri, $route)
    {
        $pattern = str_replace('/', '\/', $route);
        $pattern = preg_replace('/{[a-zA-Z0-9]+}/', '([a-zA-Z0-9\-]+)', $pattern);
        return preg_match('/^' . $pattern . '$/', $requestedUri);
    }

    private function extractParams($requestedUri, $route)
    {
        $pattern = str_replace('/', '\/', $route);
        $pattern = preg_replace('/{[a-zA-Z0-9]+}/', '([a-zA-Z0-9\-]+)', $pattern);
        preg_match('/^' . $pattern . '$/', $requestedUri, $matches);
        array_shift($matches);
        return $matches;
    }

    private function threatBody($body)
    {
        $decodedBody = json_decode($body, true);
        return is_array($decodedBody) ? $decodedBody : [];
    }
}
