<?php

namespace App\Http\Controllers;

use App\Models\Authentication;
use App\Models\User;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Route do not exist', 404]);
    }

    public function show($id)
    {
        return response()->json(['message' => 'teste']);
    }

    public function store(Request $request)
    {
        $values = ['username' => $request['username'], "password" => $request['password'], 'reason' => $request['reason']];
        $username = $request['username'];
        $password = $request['password'];
        $reason = $request['reason'];

        $validateFields = $this->returnFieldsValidation();

        $validationError = parent::validateRequestData($validateFields, $values);

        if ($validationError) {
            return $validationError;
        }

        if ($reason == 1) {
            return $this->executeLogin($username, $password);
        }

        if ($reason == 2) {
            return $this->executeLogout();
        }
    }

    private function executeLogin($username, $password)
    {
        $user = User::where('username', $username)->first();

        if ($user) {
            if (password_verify($password, $user->password)) {
                $user_auth = Authentication::where('id_user_fk', $user->id)->first();
                if ($user_auth->isLogged == 1) {
                    return response()->json(['message' => "User already logged", 'success' => false], 406);
                }
                $user_auth->isLogged = 1;
                $user_auth->login_date = gmdate('Y-m-d H:i:s');
                $user_auth->update();

                return response()->json(['user' => "$user->nickname logged with success", 'success' => true], 201);
            }
        }

        return response()->json(['error' => 'Wrong username or password', 'success' => false], 400);
    }

    private function executeLogout()
    {
    }


    private function returnFieldsValidation()
    {
        $validation = [
            'username' => [
                'validation' =>
                function ($itemValue, $field) {
                    if (is_string($itemValue)) {
                        if (strlen($itemValue) > 50 || strlen($itemValue) < 5) {
                            return response()->json(['error' => "Invalid $field value", "success" => false], 400);
                        }
                    } else {
                        return response()->json(['error' => "The $field is required and must be a string", "success" => false], 400);
                    }
                }
            ], 'password' => [
                'validation' =>
                function ($itemValue, $field) {
                    if (is_string($itemValue)) {
                        if (strlen($itemValue) > 50 || strlen($itemValue) < 5) {
                            return response()->json(['error' => "Invalid $field value", "success" => false], 400);
                        }
                    } else {
                        return response()->json(['error' => "The $field is required and must be a string", "success" => false], 400);
                    }
                }
            ],
            'reason' => [
                'validation' =>
                function ($itemValue, $field) {
                    if ($itemValue != 1) {
                        if ($itemValue != 2) {
                            return response()->json(['error' => "Must inform $field value", 'success' => false]);
                        }
                    }
                }
            ]
        ];

        return $validation;
    }
}
