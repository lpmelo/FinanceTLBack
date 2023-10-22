<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function validateRequestData($validationType, $values)
    {
        switch ($validationType) {
            case 'users':
                return $this->validateUsersData($values);
                break;
        }
    }

    private function validateUsersData($values)
    {
        foreach ($values as $itemKey => $itemValue) {
            if (is_string($itemValue)) {
                switch ($itemKey) {
                    case 'username':
                        if (strlen($itemValue) > 50) {
                            return response()->json(['error' => 'username is bigger than 50 characteres']);
                        }
                        break;
                    case 'password':
                        if (strlen($itemValue) > 50) {
                            return response()->json(['error' => 'username is bigger than 50 characteres']);
                        }
                        break;
                    case 'name':
                        if (strlen($itemValue) > 150) {
                            return response()->json(['error' => 'username is bigger than 150 characteres']);
                        }
                        break;
                    case 'nickname':
                        if (strlen($itemValue) > 10) {
                            return response()->json(['error' => 'username is bigger than 10 characteres']);
                        }
                        break;
                }
            }
        }
    }
}
