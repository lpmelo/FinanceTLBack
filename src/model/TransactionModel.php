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
    protected $recurrence_id;
    protected $plot_total_value;
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

    public function getAllByMonthAndUserId($userId, $dateRef)
    {
        if (!empty($userId)) {

            $query = "
                SELECT
                    t.id_transaction_pk,
                    tt.description as type,
                    tg.description as gender,
                    t.value,
                    t.date,
                    (case
                        when t.plot_total is not null and t.plot_number is not null
                            then CONCAT(t.plot_number, '/', t.plot_total)
                        else NULL
                    end) as plotDetail,
                    t.description 
                FROM
                    $this->table_name t 
                LEFT JOIN transaction_params tt ON t.id_type_fk = tt.id_transaction_param_pk 
                LEFT JOIN transaction_params tg ON t.id_gender_fk = tg.id_transaction_param_pk
                WHERE t.id_user_fk = $userId
                AND DATE_FORMAT(t.date, '%Y-%m-01') = '$dateRef'
            ";

            $result = $this->mysql->db_run($query);

            $result = $this->hide_fields($result, ['created_at', 'updated_at']);

            return $result;
        } else {
            return ['valid' => false, 'error' => "Has been informed a invalid userId"];
        }
    }

    public function getAllRecurrenceByUserId($userId, $dateRef)
    {
        if (!empty($userId)) {
            $query = "
            SELECT
            *
            FROM $this->table_name
            WHERE id_user_fk = $userId
            AND recurrence = 'Y'
            AND DATE_FORMAT(date, '%Y-%m-01') = '$dateRef'
            ";

            $result = $this->mysql->db_run($query);

            $result = $this->hide_fields($result, ['created_at', 'updated_at']);

            return $result;
        } else {
            return ['valid' => false, 'error' => "Has been informed a invalid userId"];
        }
    }

    public function createTransaction()
    {
        $recurrence = $this->recurrence ? 'Y' : 'N';
        $plotIdentification = $this->recurrence_id ? "'$this->recurrence_id'" : 'null';
        $plotTotal = $this->plot_total ? $this->plot_total : 'null';
        $plotNumber = $this->plot_number ? $this->plot_number : 'null';
        $plotTotalValue = $this->plot_total_value ? $this->plot_total_value : 'null';

        $query = "
        INSERT INTO $this->table_name
        (id_user_fk,
            id_type_fk,
            id_gender_fk,
            description,
            value,
            `date`,
            recurrence,
            recurrence_id,
            plot_total_value,
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
            $plotTotalValue,
            $plotTotal,
            $plotNumber,
            current_timestamp(),
            current_timestamp()
        );
        ";

        $data = $this->mysql->db_insert($query);
        return $data;
    }

    public function getBalance($userId, $dateRef)
    {
        if (!empty($userId)) {
            $query = "
                SELECT
                    SUM(t.value) as balance
                FROM transactions t 
                WHERE t.id_user_fk = $userId
                AND DATE_FORMAT(t.date, '%Y-%m-01') = '$dateRef'
            ";

            $result = $this->mysql->db_run($query);

            return $result;
        } else {
            return ['valid' => false, 'error' => "Has been informed a invalid userId"];
        }
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

    public function getRecurrenceId()
    {
        return $this->recurrence_id;
    }

    public function setRecurrenceId($recurrence_id)
    {
        return $this->recurrence_id = $recurrence_id;
    }

    public function generateUuidRecurrenceId()
    {
        $this->recurrence_id = $this->generateUuid();
    }

    public function getPlotTotalValue()
    {
        return $this->plot_total_value;
    }

    public function setPlotTotalValue($plot_total_value)
    {
        return $this->plot_total_value = $plot_total_value;
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
