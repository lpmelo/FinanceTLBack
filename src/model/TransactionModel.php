<?php

namespace Model;

use Model\Base\Model;

class TransactionModel extends Model
{
    protected $table_name = "transactions";
    protected $chavePrimaria = 'id';
    protected $camposOcultos = [];
    protected $id_transaction_pk;
    protected $id_user_fk;
    protected $id_type_fk;
    protected $id_gender_fk;
    protected $description;
    protected $value;
    protected $date;
    protected $recurrence;
    protected $plot_identification;
    protected $plot_total;
    protected $plot_number;
    protected $created_at;
    protected $updated_at;



    public function __construct()
    {
        parent::__construct($this->table_name, $this->chavePrimaria, $this->camposOcultos);
    }

    public function getAllByUserId($userId)
    {
        if (!empty($userId)) {

            $query = "
            SELECT
            *
            FROM $this->table_name
            WHERE id_user_fk = $userId
            ";

            $result = $this->mysql->db_run($query);

            if (!$result) {
                return ['valid' => false, 'error' => "There wasn't any transaction for this user"];
            }

            $result = $this->hide_fields($result, ['created_at', 'updated_at']);

            return $result;
        } else {
            return ['valid' => false, 'error' => "Has been informed a invalid userId"];
        }
    }

    public function createTransaction(){
        $recurrence = $this->recurrence ? 'Y' : 'N';
        $plotIdentification = $this->plot_identification ? $this->plot_identification : 'null';
        $plotTotal = $this->plot_total ? $this->plot_total : 'null';
        $plotNumber = $this->plot_number ? $this->plot_number : 'null';

        $query = "
        INSERT INTO $this->table_name
        (id_user_fk,
            id_type_fk,
            id_gender_fk,
            description,
            value,
            `date`,
            recurrence,
            plot_identification,
            plot_total,
            plot_number,
            created_at,
            updated_at
        )VALUES(
            $this->id_user_fk,
            $this->id_type_fk,
            $this->id_gender_fk,
            '$this->description',
            $this->value,
            '$this->date',
            '$recurrence',
            $plotIdentification,
            $plotTotal,
            $plotNumber,
            current_timestamp(),
            current_timestamp()
        );
        ";

        $data = $this->mysql->db_insert($query);
        return $data;
    }

    public function getIdTransactionPk()
    {
        return $this->id_transaction_pk;
    }

    public function setIdTransactionPk($id_transaction_pk)
    {
        $this->id_transaction_pk = $id_transaction_pk;
    }

    public function getIdUserFk()
    {
        return $this->id_user_fk;
    }

    public function setIdUserFk($id_user_fk)
    {
        $this->id_user_fk = $id_user_fk;
    }

    public function getIdTypeFk()
    {
        return $this->id_type_fk;
    }

    public function setIdTypeFk($id_type_fk)
    {
        $this->id_type_fk = $id_type_fk;
    }

    public function getIdGenderFk()
    {
        return $this->id_gender_fk;
    }

    public function setIdGenderFk($id_gender_fk)
    {
        $this->id_gender_fk = $id_gender_fk;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getRecurrence()
    {
        return $this->recurrence;
    }

    public function setRecurrence($recurrence)
    {
        $this->recurrence = $recurrence;
    }

    public function getPlotIdentification()
    {
        return $this->plot_identification;
    }

    public function setPlotIdentification($plot_identification)
    {
        return $this->plot_identification = $plot_identification;
    }

    public function generateUuidPlotIdentification()
    {
        $this->plot_identification = $this->generateUuid();
    }

    public function getPlotTotal()
    {
        return $this->plot_total;
    }

    public function setPlotTotal($plot_total)
    {
        $this->plot_total = $plot_total;
    }

    public function getPlotNumber()
    {
        return $this->plot_number;
    }

    public function setPlotNumber($plot_number)
    {
        $this->plot_number = $plot_number;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }
}
