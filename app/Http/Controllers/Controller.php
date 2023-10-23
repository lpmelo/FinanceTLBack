<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function validateRequestData($validationFields, $values)
    {
        if (isset($validationFields) && !empty($validationFields)) {
            foreach ($validationFields as $field => $fieldValue) {
                if ($fieldValue['validation'] instanceof Closure) {
                    if ($fieldValue['validation']($values[$field], $field)) {
                        return $fieldValue['validation']($values[$field], $field);
                    }
                }
            }
        }
    }

    private function validateAuthData($values)
    {
        foreach ($values as $itemKey => $itemValue) {
            if (is_string($itemValue)) {
                switch ($itemKey) {
                    case 'username':
                        if (strlen($itemValue) > 50) {
                            return response()->json(['error' => 'username is bigger than 50 characteres']);
                        } elseif (strlen($itemValue < 5)) {
                            return response()->json(['error' => 'username must be greater than 4 characters']);
                        }
                }
            } else {
                return response()->json(['error' => "The $itemKey must be a string value."]);
            }
        }
    }
}
