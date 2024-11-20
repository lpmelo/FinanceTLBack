<?php

namespace Controller;

use Controller\Base\Controller;
use Model\AuthModel;
use Model\PermissaoTelaModel;
use Model\UserModel;
use Modules\Helpers\Manager\Errors\HttpErrors;
use Modules\Helpers\Manager\Response\HttpResponse;

class authController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    public function authUser($request){
        $body = $request['data'];
        
        $valid_request = $this->validate_auth_request($body);
        if($valid_request['valid_data']){
            $credentials = [
                'username' => $body['username'],
                'password' => $body['password'],
            ];   
    
            $user = new UserModel();
            $user_data = $user->get_user_credentials($credentials);                        
            $validate_query_status = $this->validate_data_execution($user_data);
            // Toda interação com a model precisa ser validada
            if(!$validate_query_status['query_has_run']){
                return $validate_query_status['throw_error'];
            }
            if($user_data){                             
               // Cria/Atualiza a autenticação no banco e insere o token da sessão atual
               $auth = new AuthModel();
               $auth->set_id_user_fk($user_data[0]['id_user_pk']);    
               $auth->set_access_token($auth->generate_access_token($user_data[0]));         
               $session_start = $auth->insert_auth($user_data);
               // Toda interação com a model precisa ser validada
               $validate_query_status = $this->validate_data_execution($session_start);
               // Caso haja uma falha na execução da query
               if(!$validate_query_status['query_has_run']){
                   return $validate_query_status['throw_error'];
               }
               return HttpResponse::JSON([
                    "message"=>"Usuário logado com sucesso", 
                    "token" => $auth->get_access_token()
                ]);
            }else{
                return HttpErrors::code400('Dados de Autenticação inválidos');
            }
        }else{
            return HttpErrors::code400($valid_request['invalid_message']);
        }     
    }

    private function validate_auth_request($body){
        $valid_data = true;

        $required_fields = [
            'username',
            'password',
        ];

        $invalid_fields = [];

        $valid_required_fields = $this->validate_required_fields($required_fields, $body);

        $valid_data = $valid_required_fields['valid_data'];
        $invalid_fields = $valid_required_fields['invalid_fields'];

        if ($valid_data) {
            $valid_value = $this->validate_field_value($body, 'username', 'string');
            $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);      
            // A function customizada deve receber sempre 2 parametros conforme a custom_function na controller pai (valor do campo, corpo da requisicao)
            $valid_value = $this->validate_field_value($body, "password", 'customizado', function ($field_value, $request_data) {
                $validation_info = ['valid' => true, 'message' => ''];
                // Valida com base em uma regra/lógica criada especificamente para este campo
                // if($field_value == 'invalido'){
                //     $validation_info['valid'] = false;
                //     $validation_info['message'] = 'Este valor esta invalido';
                // }
                return $validation_info;
            });
            $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);        

        }
        // Somente terá valor caso algum campo esteja invalido
        $return_invalid_message = $this->prepare_string_invalid_fields($invalid_fields);

        if ($return_invalid_message) {
            $valid_data = false;
        }

        $validated_fields_info = ['valid_data' => $valid_data, 'invalid_message' => $return_invalid_message];
        return $validated_fields_info;
    }


    public function checkAuth($request)
    {
        $headers = $request['headers'];

        $valid_request = $this->validate_check_request($headers);

        if ($valid_request['valid_data']) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                $token = $matches[1];

                $authModel = new AuthModel();

                $decodedToken = $authModel->validate_token($token);

                if ($decodedToken['valid']) {
                    return HttpResponse::JSON([
                        "message" => "Token válido",
                        "token" => $token
                    ]);
                } else {
                    return HttpErrors::code401($decodedToken['error']);
                }
            }else{
                return HttpErrors::code400("O Token enviado não está no formato adequado para um Bearer Token");
            }
        } else {
            return HttpErrors::code400($valid_request['invalid_message']);
        }
    }

    private function validate_check_request($headers)
    {
        $valid_data = true;

        $required_fields = [
            'Authorization',
        ];

        $invalid_fields = [];

        $valid_required_fields = $this->validate_required_fields($required_fields, $headers);

        $valid_data = $valid_required_fields['valid_data'];
        $invalid_fields = $valid_required_fields['invalid_fields'];

        if ($valid_data) {
            $valid_value = $this->validate_field_value($headers, 'Authorization', 'string');
            $invalid_fields = $this->insert_valid_string($valid_value, $invalid_fields);
        }

        $return_invalid_message = $this->prepare_string_invalid_fields($invalid_fields);

        if ($return_invalid_message) {
            $valid_data = false;
        }

        $validated_fields_info = ['valid_data' => $valid_data, 'invalid_message' => $return_invalid_message];
        return $validated_fields_info;
    }



    public function login($requisicao){
        $requisicao['data']; //body
        $requisicao['params']; //params
        $requisicao['headers']; //headers

        //Validando dados:
        $this->validarDadosInsercaoUsuario($requisicao['data']);
    }

    private function validarDadosInsercaoUsuario($array)
    {
        $dadosCorretos = true;

        $camposValidos = [
            "fkIdPerfilUsuario",
            "fkIdRegional",
            "fkIdRegionalEspecifica",
            "fkIdEmpresa",
            "usuario",
            "nome",
            "email",
            "senha",
            "usuarioColaborador"
        ];

        $arrayCamposInvalidos = [];

        $validacaoCampos = $this->validate_required_fields($camposValidos, $array);

        $dadosCorretos = $validacaoCampos['dadosCorretos'];
        $arrayCamposInvalidos = $validacaoCampos['arrayCamposInvalidos'];

        if ($dadosCorretos) {
            $valorValidado = $this->validate_field_value($array, 'fkIdPerfilUsuario', 'uuid');
            $arrayCamposInvalidos = $this->insert_valid_string($valorValidado, $arrayCamposInvalidos);

            $valorValidado = $this->validate_field_value($array, 'fkIdRegional', 'uuid');
            $arrayCamposInvalidos = $this->insert_valid_string($valorValidado, $arrayCamposInvalidos);

            $valorValidado = $this->validate_field_value($array, 'fkIdRegionalEspecifica', 'uuid');
            $arrayCamposInvalidos = $this->insert_valid_string($valorValidado, $arrayCamposInvalidos);

            $valorValidado = $this->validate_field_value($array, 'fkIdEmpresa', 'uuid');
            $arrayCamposInvalidos = $this->insert_valid_string($valorValidado, $arrayCamposInvalidos);

            $valorValidado = $this->validate_field_value($array, 'usuario', 'string');
            $arrayCamposInvalidos = $this->insert_valid_string($valorValidado, $arrayCamposInvalidos);

            $valorValidado = $this->validate_field_value($array, 'nome', 'string');
            $arrayCamposInvalidos = $this->insert_valid_string($valorValidado, $arrayCamposInvalidos);

            $valorValidado = $this->validate_field_value($array, 'senha', 'string');
            $arrayCamposInvalidos = $this->insert_valid_string($valorValidado, $arrayCamposInvalidos);

            $valorValidado = $this->validate_field_value($array, 'usuarioColaborador', 'booleano');
            $arrayCamposInvalidos = $this->insert_valid_string($valorValidado, $arrayCamposInvalidos);


            $valorValidado = $this->validate_field_value($array, "email", 'customizado', function ($valorCampo, $arrayRequisicao) {
                $respostaValidacao = ['valido' => true, 'mensagem' => ''];

                if (str_contains($valorCampo, "@url.com.br") || str_contains($valorCampo, "@url.com.br")) {
                    $respostaValidacao['valido'] = true;
                } else {
                    $respostaValidacao['valido'] = false;
                    $respostaValidacao['mensagem'] = 'O email informado não é um e-mail pertencente ao dominio!';
                }

                return $respostaValidacao;
            });
            $arrayCamposInvalidos = $this->insert_valid_string($valorValidado, $arrayCamposInvalidos);

        }

        $camposInvalidos = $this->prepare_string_invalid_fields($arrayCamposInvalidos);

        if ($camposInvalidos) {
            $dadosCorretos = false;
        }

        $resultado = ['dadosCorretos' => $dadosCorretos, 'camposInvalidos' => $camposInvalidos];
        return $resultado;
    }
}
