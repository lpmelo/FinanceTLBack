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


        // $result = $transactionParam->getAllByCategory('GENDER');
        // $validate_query_status = $this->validate_data_execution($result);

        // if (!$validate_query_status['query_has_run']) {
        //     return $validate_query_status['throw_error'];
        // }

        // return HttpResponse::JSON([
        //     "data" => $result,
        // ]);
    }
}
