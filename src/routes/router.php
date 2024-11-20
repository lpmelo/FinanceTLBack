<?php

namespace Routes;

use Modules\Helpers\Route\Route;

class router
{
    public static function init()
    {
        return [
            Route::POST('/users/create', 'Controller\UserController', 'createUser')->middleware(["AuthMiddleware"]),
            Route::POST('/auth/login', 'Controller\AuthController', 'authUser'),
            Route::POST('/auth/check', 'Controller\AuthController', 'checkAuth'),
            Route::GET('/transaction-params/genders', 'Controller\TransactionParamController', 'getAllGenders')->middleware(["AuthMiddleware"]),
            Route::GET('/transaction-params/types', 'Controller\TransactionParamController', 'getAllTypes')->middleware(["AuthMiddleware"]),
        ];
    }
}
