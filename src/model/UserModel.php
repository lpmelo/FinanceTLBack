<?php

namespace Model;

use Model\Base\Model;

class UserModel extends Model
{
    protected $mysql;
    protected $table_name = "users";
    protected $primary_key = 'id';
    protected $hidden_fields = [];
    protected $mail;
    protected $name;
    protected $password;
    protected $nickname;
    protected $username;

    public function __construct()
    {
        parent::__construct($this->table_name, $this->primary_key, $this->hidden_fields);
    }

    public function create_user()
    {
        $user_query = "
            INSERT INTO $this->table_name(
                name, 
                mail
            )VALUES(   
                '$this->name',
                '$this->mail'
            );
        ";
        $data = $this->mysql->db_insert($user_query);
        return $data;
    }


    public function get_user_credentials($credentials)
    {
        // Busca no banco user e password com os parametros, retorna body do user se encontrou se não retorna false
        $select_user_info = "
            SELECT 
                *
            FROM users 
            WHERE (username = '" . $credentials['username'] . "'
            OR mail = '" . $credentials['username'] . "')
            AND password = '" . $credentials['password'] . "'
        ";

        $user_info = $this->mysql->db_run($select_user_info);
        $user_info = $this->hide_fields($user_info, ['created_at', 'password', 'updated_at']);
        return $user_info;
    }

    public function getUserById($userId)
    {
        $select_user_info = "
            SELECT 
                *
            FROM $this->table_name 
            WHERE id_user_pk = $userId
        ";


        $user_info = $this->mysql->db_run($select_user_info);

        if (!$user_info) {
            return ['valid' => false, 'error' => "There wasn't a user with this id"];
        }

        $user_info = $this->hide_fields($user_info, ['created_at', 'password', 'updated_at']);
        return $user_info;
    }

    public function get_mail()
    {
        return $this->mail;
    }

    public function set_mail($mail)
    {
        $this->mail = $mail;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function set_name($name)
    {
        $this->name = $name;
    }

    public function get_nickname()
    {
        return $this->nickname;
    }

    public function set_nickname($nickname)
    {
        $this->nickname = $nickname;
    }

    public function get_username()
    {
        return $this->username;
    }

    public function set_username($username)
    {
        $this->name = $username;
    }

    public function get_password()
    {
        return $this->username;
    }

    public function set_password($username)
    {
        $this->name = $username;
    }
}
