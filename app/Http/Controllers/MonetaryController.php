<?php

namespace App\Http\Controllers;

use App\Models\MoneyEntrie;
use App\Models\MoneyExit;
use App\Models\User;
use Illuminate\Http\Request;

class MonetaryController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Route do not exist', 404]);
    }

    public function show($id)
    {
    }

    public function store(Request $request)
    {
        $values = [
            'id_user' => $request['id_user'],
            'description' => $request['description'],
            'value' => $request['value'],
            'type' => $request['type'],
            'entrie_date' => $request['entrie_date'],
            'exit_date' => $request['exit_date']
        ];

        $idUser = $request['id_user'];
        $description = $request['description'];
        $value = $request['value'];
        $type = $request['type'];
        $entrieDate = $request['entrie_date'];
        $exitDate = $request['exit_date'];

        $validateFields = $this->returnMonetaryRegisterValidation();

        $validationError = parent::validateRequestData($validateFields, $values);

        if ($validationError) {
            return $validationError;
        }

        if ($type === 'entrie') {
            return $this->registerEntrie($idUser, $description, $value, $entrieDate);
        }

        if ($type === "exit") {
            return $this->registerExit($idUser, $description, $value, $exitDate);
        }
    }

    private function registerEntrie($idUser, $description, $value, $entrieDate)
    {
        $user = User::where('id', $idUser)->first();

        if ($user) {
            $entrie = MoneyEntrie::create([
                'description' => $description,
                'value' => $value,
                'id_user_fk' => $idUser,
                'entrie_date' => $entrieDate
            ]);

            $generatedId = $entrie['id_entrie'];

            if ($generatedId) {
                return response()->json(['message' => "Entrie registered", "success" => true], 201);
            }
        }

        return response()->json(['error' => "User with id $idUser doesn't exist", 'success' => false], 400);
    }

    private function registerExit($idUser, $description, $value, $exitDate)
    {
        $user = User::where('id', $idUser)->first();

        if ($user) {
            $entrie = MoneyExit::create([
                'description' => $description,
                'value' => $value,
                'id_user_fk' => $idUser,
                'exit_date' => $exitDate
            ]);

            $generatedId = $entrie['id_exit'];

            if ($generatedId) {
                return response()->json(['message' => "Exit registered", "success" => true], 201);
            }
        }

        return response()->json(['error' => "User with id $idUser doesn't exist", 'success' => false], 400);
    }

    private function returnMonetaryRegisterValidation()
    {
        $validation = [
            'type' => [
                'validation' =>
                function ($itemValue, $field, $values) {
                    if (is_string($itemValue)) {
                        if ($itemValue != 'entrie' && $itemValue != 'exit') {
                            return response()->json(['error' => "Invalid $field value", "success" => false], 400);
                        }
                    } else {
                        return response()->json(['error' => "The $field is required and must be a string", "success" => false], 400);
                    }
                }
            ], 'id_user' => [
                'validation' =>
                function ($itemValue, $field, $values) {
                    if (is_int($itemValue)) {
                        if (empty($itemValue)) {
                            return response()->json(['error' => "Invalid $field value", "success" => false], 400);
                        }
                    } else {
                        return response()->json(['error' => "The $field is required and must be a valid integer", "success" => false], 400);
                    }
                }
            ],
            'description' => [
                'validation' =>
                function ($itemValue, $field) {
                    if (is_string($itemValue)) {
                        if (strlen($itemValue) > 50) {
                            return response()->json(['error' => "Invalid $field value", "success" => false], 400);
                        }
                    } else {
                        return response()->json(['error' => "The $field is required and must be a string", "success" => false], 400);
                    }
                }
            ],
            'value' => [
                'validation' =>
                function ($itemValue, $field, $values) {
                    if (is_numeric($itemValue)) {
                        if (empty($itemValue)) {
                            return response()->json(['error' => "Invalid $field value", "success" => false], 400);
                        }
                    } else {
                        return response()->json(['error' => "The $field is required and must be a number", "success" => false], 400);
                    }
                }
            ],
            'entrie_date' => [
                'validation' =>
                function ($itemValue, $field, $values) {
                    if (array_key_exists('type', $values)) {
                        if ($values['type'] === 'entrie') {
                            if (empty($itemValue)) {
                                return response()->json(['error' => "Invalid $field value", "success" => false], 400);
                            } else {
                                $pattern = '/^\d{4}-\d{2}-\d{2}$/';

                                if (!preg_match($pattern, $itemValue)) {
                                    return response()->json(['error' => "Invalid $field value", "success" => false], 400);
                                } else {
                                    list($year, $month, $day) = explode('-', $itemValue);

                                    if (!checkdate($month, $day, $year)) {
                                        return response()->json(['error' => "Invalid $field value", "success" => false], 400);
                                    }
                                }
                            }
                        }
                    }
                }
            ],
            'exit_date' => [
                'validation' =>
                function ($itemValue, $field, $values) {
                    if (array_key_exists('type', $values)) {
                        if ($values['type'] === 'exit') {
                            if (empty($itemValue)) {
                                return response()->json(['error' => "Invalid $field value", "success" => false], 400);
                            } else {
                                $pattern = '/^\d{4}-\d{2}-\d{2}$/';

                                if (!preg_match($pattern, $itemValue)) {
                                    return response()->json(['error' => "Invalid $field value", "success" => false], 400);
                                } else {
                                    list($year, $month, $day) = explode('-', $itemValue);

                                    if (!checkdate($month, $day, $year)) {
                                        return response()->json(['error' => "Invalid $field value", "success" => false], 400);
                                    }
                                }
                            }
                        }
                    }
                }
            ],
        ];

        return $validation;
    }
}
