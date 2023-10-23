<?php

namespace App\Http\Controllers;

use App\Models\Authentication;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users]);
    }

    public function show($id)
    {
        return response()->json(['message' => 'teste']);
    }

    public function store(Request $request)
    {
        $username = $request['username'];
        $password = $request['password'];
        $name = $request['name'];
        $nickname = $request['nickname'];

        $values = ['username' => $request['username'], 'password' => $request['password'], 'name' => $request['name'], 'nickname' => $request['nickname']];

        $validateFields = $this->returnFieldsValidation();

        $validationError = parent::validateRequestData($validateFields, $values);

        if ($validationError) {
            return $validationError;
        };

        $user = User::create([
            'username' => $username,
            'password' => bcrypt($password),
            'name' => $name,
            'nickname' => $nickname,
        ]);

        $generatedId = $user['id'];

        Authentication::create([
            'isLogged' => false,
            'login_date' => null,
            'id_user_fk' => $generatedId,
        ]);

        return response()->json(['message' => "User with id $generatedId created successfully", "success" => true], 201);
    }

    private function returnFieldsValidation()
    {
        $validation = [
            'username' => [
                'validation' =>
                function ($itemValue, $field) {
                    if (is_string($itemValue)) {
                        if (User::where('username', $itemValue)->first()) {
                            return response()->json(['error' => "User with username: '$itemValue' already exist.", "success" => false], 409);
                        }
                        if (strlen($itemValue) > 50) {
                            return response()->json(['error' => "$field is bigger than 50 characteres", "success" => false], 400);
                        } elseif (strlen($itemValue) < 5) {
                            return response()->json(['error' => "$field must be greater than 4 characters", "success" => false], 400);
                        }
                    } else {
                        return response()->json(['error' => "The $field is required and must be a string", "success" => false], 400);
                    }
                }
            ], 'password' => [
                'validation' =>
                function ($itemValue, $field) {
                    if (is_string($itemValue)) {
                        if (strlen($itemValue) > 50) {
                            return response()->json(['error' => "$field is bigger than 50 characteres", "success" => false], 400);
                        } elseif (strlen($itemValue) < 5) {
                            return response()->json(['error' => "$field must be greater than 4 characters", "success" => false], 400);
                        }
                    } else {
                        return response()->json(['error' => "The $field is required and must be a string", "success" => false], 400);
                    }
                }
            ], 'name' => [
                'validation' =>
                function ($itemValue, $field) {
                    if (is_string($itemValue)) {
                        if (strlen($itemValue) > 150) {
                            return response()->json(['error' => "$field is bigger than 150 characteres", "success" => false], 400);
                        }
                    } else {
                        return response()->json(['error' => "The $field is required and must be a string", "success" => false], 400);
                    }
                }
            ], 'nickname' => [
                'validation' => function ($itemValue, $field) {
                    if (is_string($itemValue)) {

                        if (strlen($itemValue) > 10) {
                            return response()->json(['error' => "$field is bigger than 10 characteres", "success" => false], 400);
                        }
                    } else {
                        return response()->json(['error' => "The $field is required and must be a string", "success" => false], 400);
                    }
                }
            ]
        ];

        return $validation;
    }
}
