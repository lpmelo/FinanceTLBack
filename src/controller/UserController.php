<?php

namespace Controller;

use Controller\Base\Controller;
use Model\UserModel;
use Modules\Helpers\Manager\Errors\HttpErrors;
use Modules\Helpers\Manager\Response\HttpResponse;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function createUser($requisicao)
    {
        $data = $requisicao['data'];
        $validate_info = $this->validate_create_user($data);
        if ($validate_info['validFields']) {
            $user_model = new UserModel();
            $user_model->set_username($data['username']);
            $user_model->set_nickname($data['nickname']);
            $user_model->set_name($data['name']);
            $user_model->set_mail($data['mail']);
            $user_model->set_password($data['password']);
            $body = $user_model->create_user();

            if ($body['id'] == 1) {
                return HttpResponse::JSON([
                    'message' => 'Sucesso!',
                    'createdId' => $body['id']
                ]);
            } else {
                return HttpErrors::code500($body['message']);
            }

        } else {
            return HttpErrors::code400($validate_info['invalidFields']);
        }
    }

    private function validate_create_user($request_data)
    {
        $valid_data = true;

        $valid_fields = [
            "nickname",
            "username",
            "name",
            "mail",
            "password"
        ];

        $arrayCamposInvalidos = [];

        $fields_validation = $this->validate_required_fields($valid_fields, $request_data);

        $valid_data = $fields_validation['valid_data'];
        $invalid_fields = $fields_validation['invalid_fields'];

        if ($valid_data) {
            $valid_value = $this->validate_field_value($request_data, 'username', 'string');
            $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);

            $valid_value = $this->validate_field_value($request_data, 'name', 'string');
            $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);

            $valid_value = $this->validate_field_value($request_data, 'nickname', 'string');
            $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);

            $valid_value = $this->validate_field_value($request_data, 'password', 'string');
            $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);

            $validated_field = $this->validate_field_value($request_data, 'mail', 'custom', function ($value, $request_data) {
                $validate_info = ['valid' => true, 'message' => ''];
                $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
                $valid_mail = preg_match($regex, $value);
                $validate_info['valid'] = $valid_mail;
                if (!$valid_mail) {
                    $validate_info['message'] = 'O e-mail digitado é invalido.';
                }

                return $validate_info;
            });

            $invalid_fields = $this->insert_valid_string($validated_field, $invalid_fields);


        }

        $invalid_fields = $this->prepare_string_invalid_fields($invalid_fields);

        if ($invalid_fields) {
            $valid_data = false;
        }

        $resultado = ['validFields' => $valid_data, 'invalidFields' => $invalid_fields];
        return $resultado;
    }
}
