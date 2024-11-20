<?php

namespace Controller;

use Controller\Base\Controller;
use Model\AuthModel;
use Model\PermissaoTelaModel;
use Model\TransactionParamModel;
use Model\UserModel;
use Modules\Helpers\Manager\Errors\HttpErrors;
use Modules\Helpers\Manager\Response\HttpResponse;

class TransactionParamController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllGenders($request)
    {
        $transactionParam = new TransactionParamModel();

        $result = $transactionParam->getAllByCategory('GENDER');
        $validate_query_status = $this->validate_data_execution($result);

        if (!$validate_query_status['query_has_run']) {
            return $validate_query_status['throw_error'];
        }

        return HttpResponse::JSON([
            "data" => $result,
        ]);
    }

    public function getAllTypes($request)
    {
        $transactionParam = new TransactionParamModel();

        $result = $transactionParam->getAllByCategory('TYPE');
        $validate_query_status = $this->validate_data_execution($result);

        if (!$validate_query_status['query_has_run']) {
            return $validate_query_status['throw_error'];
        }

        return HttpResponse::JSON([
            "data" => $result,
        ]);
    }
}
