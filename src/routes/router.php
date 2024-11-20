<?php

namespace Routes;

use Modules\Helpers\Route\Route;

class router
{
    public static function init()
    {
        return [
            // Rotas Usuarios
            // Route::GET('/users', 'Controller\UsuarioController', 'buscarTodosAtivos')->middleware(["AutenticacaoMiddleware"]),
            // Route::POST('/users', 'Controller\UsuarioController', 'registrarUsuario'),                        
            // Route::DELETE('/users/delete/{id}', 'Controller\UsuarioController', 'deletarId')->middleware(["AutenticacaoMiddleware"]),
            // Route::PUT('/regional-goal/edit/{id}', 'Controller\MetaController', 'editarMetaRegional')->middleware(["AutenticacaoMiddleware"]), 
            Route::POST('/users/create', 'Controller\UserController', 'createUser')->middleware(["AuthMiddleware"]),                                          
            Route::POST('/auth/login', 'Controller\AuthController', 'authUser'),
            Route::POST('/auth/check', 'Controller\AuthController', 'checkAuth')
        ];
    }
}
