<?php

namespace Controller;

use Controller\Base\Controller;
use Model\TransactionModel;
use Model\UserModel;
use Modules\Helpers\Manager\Errors\HttpErrors;
use Modules\Helpers\Manager\Response\HttpResponse;

class TransactionController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllTransactionsByUserId($request)
    {
        $params = $request['params'];

        if (!empty($params)) {
            $userId = $params[0];
            $user = new UserModel();

            $result = $user->getUserById($userId);
            $validate_query_status = $this->validate_data_execution($result);

            if (!$validate_query_status['query_has_run']) {
                return $validate_query_status['throw_error'];
            }

            $validQueryResult = $this->validQueryResult($result);

            if (!$validQueryResult) {
                return HttpErrors::code400($result);
            }



            $transactions = new TransactionModel();
            $result = $transactions->getAllByUserId($userId);

            $validate_query_status = $this->validate_data_execution($result);

            if (!$validate_query_status['query_has_run']) {
                return $validate_query_status['throw_error'];
            }

            $validQueryResult = $this->validQueryResult($result);

            if (!$validQueryResult) {
                return HttpErrors::code400($result);
            }

            return HttpResponse::JSON($result);
        }

        return HttpErrors::code400("User Id hasn't informed");
    }

    public function createTransaction($request) {}

    private function validateCreateTransaction($request_data)
    {
        $valid_data = true;

        $requiredFields = [
            "description",
            "value",
            "date"
        ];

        $fields_validation = $this->validate_required_fields($requiredFields, $request_data);

        $valid_data = $fields_validation['valid_data'];
        $invalid_fields = $fields_validation['invalid_fields'];

        if ($valid_data) {
            $valid_value = $this->validate_field_value($request_data, 'description', 'string');
            $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);

            $valid_value = $this->validate_field_value($request_data, 'value', 'number');
            $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);

            $validated_field = $this->validate_field_value($request_data, 'date', 'datetime');

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
