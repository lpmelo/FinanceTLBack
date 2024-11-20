<?php

namespace Modules\Data;

use PDO;
use Exception;

class mysqlconnector
{
    private $host;
    private $username;
    private $password;
    private $database;
    private $port;
    private $ambiente;
    public $conn = null;
    public $connError = null;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];
        $this->database = $_ENV['DB_DATABASE'];
        $this->port = $_ENV['DB_PORT'];
        $this->ambiente = $_ENV['AMBIENTE'];
        $this->connect_my_sql();
    }

    public function connect_my_sql()
    {
        // Opções da conexão PDO
        if ($this->ambiente == 'planejamento') {
            $opcoesConexao = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Desabilitar SSL
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::MYSQL_ATTR_SSL_CA => true,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ];
        } else {
            $opcoesConexao = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
        }

        $stringConexao = "mysql:host=$this->host;dbname=$this->database;port=$this->port";
        try {
            $this->conn = new PDO($stringConexao, $this->username, $this->password, $opcoesConexao);
            return $this->conn;

        } catch (Exception $er) {
            $this->connError = "Falha de conexão: " . $er->getMessage();
        }
    }

    public function end_connection()
    {
        return $this->conn = null;
    }

    public function db_run($query)
    {
        if (isset($this->conn)) {
            $conn = $this->conn;
            $cursor = $conn->prepare($query);

            try {
                $cursor->execute();

                if ($cursor->columnCount() > 0) {
                    return $cursor->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $affectedRows = $cursor->rowCount();
                    return ['id'=> 1,'message' => "Sucesso!", 'affectedRows' => $affectedRows];
                }
            } catch (Exception $er) {
                $errorMessage = $er->getMessage();
                $error = ['id' => -1, 'message' => $errorMessage];
                return $error;
            }
        } else {
            return ['id' => -1, 'message' => 'Conexão ao banco de dados não estabelecida.'];
        }
    }


    public function db_insert($query){
        $mysql = new MySqlConnector();
        $conn = $mysql->conn;
        $connError = $mysql->connError;
        if (empty($connError)) {            
            $resultado = $mysql->db_run($query);
            $mysql->end_connection();
            return $resultado;
        } else {
            return ['id' => -1, 'message' => $connError];
        }
    }




}
