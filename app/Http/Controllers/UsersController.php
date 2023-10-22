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

        $validationError = parent::validateRequestData('users', $values);

        if ($validationError) {
            return $validationError;
        };

        if (User::where('username', $username)->first()) {
            return response()->json(['message' => "User with username $username already exists!"], 409);
        }

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

        return response()->json(['message' => "User with id $generatedId created successfully"], 201);
    }
}
