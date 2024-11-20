<?php

namespace Middlewares;

use Middlewares\Base\Middleware;
use Model\AuthModel;

class AuthMiddleware extends Middleware
{
    protected $autorizar;

    public function __construct($request)
    {
        $authorize = $this->verify_token($request);
        parent::__construct($authorize);
    }

    private function verify_token($request)
    {       
        $header_request = $request['headers'];
        if (array_key_exists('Authorization', $header_request)) {
            $token = $header_request['Authorization'];
            $token = str_replace('Bearer ', '', $token);                   
            $auth_model = new AuthModel();
            $result = $auth_model->validate_token($token);

            if(!$result['valid']){
                return "Token inválido.";
            }                
            return $result['valid'];
        }
        return "Token inválido.";
    }
}
