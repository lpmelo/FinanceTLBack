<?php

namespace Controller;

use Controller\Base\Controller;
use Model\TransactionModel;
use Model\TransactionParamModel;
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

    public function createTransaction($request)
    {
        $body = $request['data'];

        $validation = $this->validateCreateTransaction($body);

        if ($validation['validFields']) {
            $description = $body['description'];
            $value = $body['value'];
            $idUserFk = $body['id_user_fk'];
            $date = $body['date'];
            $idTypeFk = array_key_exists('id_type_fk', $body) ? $body['id_type_fk'] : null;
            $idGenderFk = array_key_exists('id_gender_fk', $body) ? $body['id_gender_fk'] : null;
            $plotTotal = array_key_exists('plot_total', $body) ? $body['plot_total'] : null;
            $recurrence = array_key_exists('recurrence', $body) ? $body['recurrence'] : (($plotTotal && $idGenderFk == 1) ? true : null);


            $userModel = new UserModel();
            $result = $userModel->getUserById($idUserFk);

            $validate_query_status = $this->validate_data_execution($result);

            if (!$validate_query_status['query_has_run']) {
                return $validate_query_status['throw_error'];
            }

            $transactionModel = new TransactionModel();
            $transactionParamsModel = new TransactionParamModel();

            if (!empty($idTypeFk)) {
                $result = $transactionParamsModel->getTypeById($idTypeFk);

                $validate_query_status = $this->validate_data_execution($result);

                if (!$validate_query_status['query_has_run']) {
                    return $validate_query_status['throw_error'];
                }

                $transactionModel->setIdTypeFk($idTypeFk);
            } else {
                $transactionType = $value > 0 ? 4 : 5;
                $transactionModel->setIdTypeFk($transactionType);
            }

            if (!empty($idGenderFk)) {
                $result = $transactionParamsModel->getGenderById($idGenderFk);

                $validate_query_status = $this->validate_data_execution($result);

                if (!$validate_query_status['query_has_run']) {
                    return $validate_query_status['throw_error'];
                }

                if ($idGenderFk == 1 && !$plotTotal) {
                    return HttpErrors::code400("Isn't possible to create a plot transaction without the total of the plots to be calculated");
                }

                $transactionModel->setIdGenderFk($idGenderFk);
            } else {
                $transactionModel->setIdGenderFk(3);
            }

            if ($recurrence || ($plotTotal && $idGenderFk == 1)) {
                $transactionModel->setRecurrence($recurrence);
            }

            if ($plotTotal && $idGenderFk == 1) {
                if ($value > 0) {
                    $plotValue = $value / $plotTotal;

                    $transactionModel->setPlotTotal($plotTotal);
                    $transactionModel->setPlotNumber(1);
                    $transactionModel->generateUuidPlotIdentification();
                    $transactionModel->setPlotTotalValue($value);
                    $transactionModel->setValue($plotValue);
                } else {
                    return HttpErrors::code400("Isn't possible to create a plot transaction with negative values!");
                }
            }

            $transactionModel->setIdUserFk($idUserFk);
            $transactionModel->setDescription($description);
            $transactionModel->setDate($date);

            if (!$transactionModel->getValue()) {
                $transactionModel->setValue($value);
            }

            $result = $transactionModel->createTransaction();

            $validate_query_status = $this->validate_data_execution($result);

            if (!$validate_query_status['query_has_run']) {
                return $validate_query_status['throw_error'];
            }

            return HttpResponse::JSON([
                'success' => true,
                'message' => 'Transação criada com sucesso!'
            ]);
        }
        return HttpErrors::code400($validation['invalidFields']);
    }

    private function validateCreateTransaction($request_data)
    {
        $valid_data = true;

        $requiredFields = [
            "id_user_fk",
            "description",
            "value",
            "date"
        ];

        $fields_validation = $this->validate_required_fields($requiredFields, $request_data);

        $valid_data = $fields_validation['valid_data'];
        $invalid_fields = $fields_validation['invalid_fields'];

        if ($valid_data) {
            $valid_value = $this->validate_field_value($request_data, 'id_user_fk', 'number');
            $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);

            $valid_value = $this->validate_field_value($request_data, 'description', 'string');
            $valid_value = empty($request_data['description']) ? 'description está vazio' : "";
            $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);

            $valid_value = $this->validate_field_value($request_data, 'value', 'number');
            $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);

            $validated_field = $this->validate_field_value($request_data, 'date', 'datetime');
            $invalid_fields = $this->insert_valid_string($validated_field, $invalid_fields);

            if (array_key_exists('recurrence', $request_data)) {
                $valid_value = $this->validate_field_value($request_data, 'recurrence', 'boolean');
                $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);
            }

            if (array_key_exists('id_type_fk', $request_data)) {
                $valid_value = $this->validate_field_value($request_data, 'id_type_fk', 'number');
                $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);
            }

            if (array_key_exists('id_gender_fk', $request_data)) {
                $valid_value = $this->validate_field_value($request_data, 'id_gender_fk', 'number');
                $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);
            }

            if (array_key_exists('plot_total', $request_data)) {
                $valid_value = $this->validate_field_value($request_data, 'plot_total', 'number');
                $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);
            }
        }

        $invalid_fields = $this->prepare_string_invalid_fields($invalid_fields);

        if ($invalid_fields) {
            $valid_data = false;
        }

        $resultado = ['validFields' => $valid_data, 'invalidFields' => $invalid_fields];
        return $resultado;
    }
}
