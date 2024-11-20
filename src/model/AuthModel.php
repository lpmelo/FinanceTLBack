<?php

namespace Model;

use DateTime;
use Exception;
use Model\Base\Model;
use Modules\Data\MySqlConnector;
use Modules\Helpers\Manager\Errors\HttpErrors;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class AuthModel extends Model
{
    protected $table_name = "authentication";
    protected $chavePrimaria = 'id_autenticacao';
    protected $camposOcultos = [];        
    protected $id_user_fk;
    protected $access_token;

    public function __construct()
    {
        parent::__construct($this->table_name, $this->chavePrimaria, $this->camposOcultos);
    }

    public function validate_token($token)
    {
        $decodedToken = $this->decode_access_token($token);

        if ($decodedToken['valid']) {
            $check_token = "
                    SELECT
                        access_token
                    FROM authentication
                    WHERE access_token = '" . $token . "'
                ";

            $token_info = $this->mysql->db_run($check_token);

            if (!$token_info) {
                return ['valid' => false, 'error' => "This token does not exist on database"];
            }


            $user_data = $decodedToken['decode']->data;

            $select_user_data_and_compare = "
                    SELECT 
                        id_user_pk,
                        username
                    FROM users
                    WHERE id_user_pk = '" . $user_data->id_user_pk . "'
                ";

            $user_info = $this->mysql->db_run($select_user_data_and_compare);

            if (!$user_info) {
                return ['valid' => false, 'error' => "There is no user with id: $user_data->id_user_pk"];
            }
        }

        return $decodedToken;
    }

    public function insert_auth($body){

        $select_auth_info = "
            SELECT 
              id_user_fk
            FROM `$this->table_name` 
            WHERE id_user_fk = ".$this->id_user_fk."
        ";

        $auth_info = $this->mysql->db_run($select_auth_info);

        if($auth_info){
            $update_auth = "
                UPDATE `$this->table_name`
                SET
                    status = 1,
                    login_date = NOW(),
                    updated_at = NOW(),
                    access_token = '".$this->access_token."'
                WHERE id_user_fk = ".$this->id_user_fk."
            ";

            $exec_auth = $this->mysql->db_run($update_auth);
        }else{
            $insert_auth = "
                INSERT INTO `$this->table_name`(          
                    status, 
                    login_date, 
                    id_user_fk, 
                    created_at, 
                    updated_at,
                    access_token
                )VALUES(
                    1, 
                    NOW(), 
                    ".$this->id_user_fk.", 
                    NOW(), 
                    NOW(),
                    '".$this->access_token."' 
                ); 
            ";

            $exec_auth =  $this->mysql->db_insert($insert_auth);

        }

       

        return $exec_auth;
    }

    public function get_id_user_fk(){
        return $this->id_user_fk;
    }

    public function set_id_user_fk($id_user_fk){
        $this->id_user_fk = $id_user_fk;
    }

    public function get_access_token(){
        return $this->access_token;
    }

    public function set_access_token($access_token){
        $this->access_token = $access_token;
    }

}
