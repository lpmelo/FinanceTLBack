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
                $transactionModel->generateUuidRecurrenceId();
            }

            if ($plotTotal && $idGenderFk == 1) {
                if ($value > 0) {
                    $plotValue = $value / $plotTotal;

                    $transactionModel->setPlotTotal($plotTotal);
                    $transactionModel->setPlotNumber(1);
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

    public function getMonthTransactions($request)
    {
        $body = $request['data'];
        $validation = $this->validateGetMonthTransactions($body);

        if ($validation['validFields']) {

            $idUserFk = $body['id_user_fk'];
            $dateRef = $body['date_ref'];

            $userModel = new UserModel();
            $result = $userModel->getUserById($idUserFk);

            $validate_query_status = $this->validate_data_execution($result);

            if (!$validate_query_status['query_has_run']) {
                return $validate_query_status['throw_error'];
            }

            $transactionModel = new TransactionModel();
            $previousMonth = $this->returnDateSubtractingMonths($dateRef, 1);
            $actualUserTransactions = $transactionModel->getAllByMonthAndUserId($idUserFk, $dateRef);
            $previousRecurrenceTransactions = $transactionModel->getAllRecurrenceByUserId($idUserFk, $previousMonth);

            if (!empty($actualUserTransactions)) {
                foreach ($previousRecurrenceTransactions as $index => $transactionData) {
                    if (isset($transactionData['recurrence_id'])) {
                        $arrayKey = array_search($transactionData['recurrence_id'], array_column($actualUserTransactions, 'recurrence_id'));

                        if ($arrayKey == false && $arrayKey != 0) {
                            $transactionMissed = array($previousRecurrenceTransactions[$index]);

                            $this->createTransactionByData($idUserFk, $transactionModel, $transactionMissed);
                        }
                    }
                }
            } else {
                if (!empty($previousRecurrenceTransactions)) {
                    $this->createTransactionByData($idUserFk, $transactionModel, $previousRecurrenceTransactions);
                }
            }


            $userTransactions = $transactionModel->getAllByMonthAndUserId($idUserFk, $dateRef);


            return HttpResponse::JSON($userTransactions);
        }

        return HttpErrors::code400($validation['invalidFields']);
    }

    private function createTransactionByData($idUserFk, $transactionModelInstance, $transactionArray)
    {
        if (!empty($transactionArray) && ($transactionModelInstance instanceof TransactionModel) && !empty($idUserFk)) {
            foreach ($transactionArray as $transactionData) {
                $plotNumber = $transactionData['plot_number'] != null ?  $transactionData['plot_number'] + 1 : null;

                if ($transactionData['id_gender_fk'] != 1 || ($transactionData['id_gender_fk'] == 1 && (($transactionData['plot_number'] + 1) <= $transactionData['plot_total']))) {
                    $transactionModelInstance->setIdUserFk($idUserFk);
                    $transactionModelInstance->setIdTypeFk($transactionData['id_type_fk']);
                    $transactionModelInstance->setIdGenderFk($transactionData['id_gender_fk']);


                    $transactionModelInstance->setRecurrence(true);


                    $transactionModelInstance->setRecurrenceId($transactionData['recurrence_id']);
                    $transactionModelInstance->setPlotTotal($transactionData['plot_total']);
                    $transactionModelInstance->setPlotNumber($plotNumber);
                    $transactionModelInstance->setPlotTotalValue($transactionData['plot_total_value']);


                    $transactionModelInstance->setDescription($transactionData['description']);
                    $transactionModelInstance->setDate($this->returnDateAddingMonths($transactionData['date'], 1));
                    $transactionModelInstance->setValue($transactionData['value']);
                    $transactionModelInstance->createTransaction();
                }
            }
        }
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

    private function validateGetMonthTransactions($request_data)
    {
        $valid_data = true;

        $requiredFields = [
            "id_user_fk",
            "date_ref"
        ];

        $fields_validation = $this->validate_required_fields($requiredFields, $request_data);

        $valid_data = $fields_validation['valid_data'];
        $invalid_fields = $fields_validation['invalid_fields'];

        if ($valid_data) {
            $valid_value = $this->validate_field_value($request_data, 'id_user_fk', 'number');
            $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);

            $validated_field = $this->validate_field_value($request_data, 'date_ref', 'date');
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
