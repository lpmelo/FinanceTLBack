<?php

namespace Model;

use Model\Base\Model;

class TransactionParamModel extends Model
{
    protected $table_name = "transaction_params";
    protected $chavePrimaria = 'id_transaction_param_pk';
    protected $camposOcultos = [];
    protected $id_transaction_param_pk;
    protected $description;
    protected $category;
    protected $created_at;
    protected $updated_at;

    public function __construct()
    {
        parent::__construct($this->table_name, $this->chavePrimaria, $this->camposOcultos);
    }

    public function getAllByCategory($category)
    {
        $validCategories = ['GENDER', 'TYPE'];

        if ($category && in_array($category, $validCategories)) {

            $query = "
            SELECT
            *
            FROM $this->table_name
            WHERE category = '$category'
            ";

            $result = $this->mysql->db_run($query);

            if (!$result) {
                return ['valid' => false, 'error' => "There wasn't any transaction genders"];
            }

            $result = $this->hide_fields($result, ['created_at', 'updated_at']);

            return $result;
        } else {
            return ['valid' => false, 'error' => "Has been informed a invalid category"];
        }
    }

    public function getById($transactionParamId)
    {
        $query = "
            SELECT
            *
            FROM $this->table_name
            WHERE $this->chavePrimaria = '$transactionParamId'
            ";

        $result = $this->mysql->db_run($query);

        if (!$result) {
            return ['valid' => false, 'error' => "There wasn't any transaction param with this Id"];
        }

        $result = $this->hide_fields($result, ['created_at', 'updated_at']);

        return $result;
    }

    public function getGenderById($transactionParamId)
    {
        $query = "
            SELECT
            *
            FROM $this->table_name
            WHERE $this->chavePrimaria = '$transactionParamId'
            AND category = 'GENDER';
            ";

        $result = $this->mysql->db_run($query);

        if (!$result) {
            return ['valid' => false, 'error' => "There wasn't any transaction param of category GENDER with this Id"];
        }

        $result = $this->hide_fields($result, ['created_at', 'updated_at']);

        return $result;
    }

    public function getTypeById($transactionParamId)
    {
        $query = "
            SELECT
            *
            FROM $this->table_name
            WHERE $this->chavePrimaria = '$transactionParamId'
            AND category = 'TYPE';
            ";

        $result = $this->mysql->db_run($query);

        if (!$result) {
            return ['valid' => false, 'error' => "There wasn't any transaction param of category TYPE with this Id"];
        }

        $result = $this->hide_fields($result, ['created_at', 'updated_at']);

        return $result;
    }

    public function getByDescription($description)
    {
        $query = "
            SELECT
            *
            FROM $this->table_name
            WHERE description = '$description'
            ";

        $result = $this->mysql->db_run($query);

        if (!$result) {
            return ['valid' => false, 'error' => "There wasn't any transaction param with this description"];
        }

        $result = $this->hide_fields($result, ['created_at', 'updated_at']);

        return $result;
    }

    public function get_id()
    {
        return $this->id_transaction_param_pk;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function set_description($description)
    {
        $this->description = $description;
    }

    public function get_category()
    {
        return $this->category;
    }

    public function set_category($category)
    {
        $this->category = $category;
    }

    public function get_created_at()
    {
        return $this->created_at;
    }

    public function set_created_at($created_at)
    {
        $this->created_at = $created_at;
    }

    public function get_updated_at()
    {
        return $this->updated_at;
    }

    public function set_updated_at($updated_at)
    {
        return $this->updated_at = $updated_at;
    }
}
