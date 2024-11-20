<?php

namespace Controller\Base;

use Modules\Helpers\Manager\Errors\HttpErrors;
use DateTime;


class Controller
{
    public function __construct() {}

    /**
     * Função validate_required_fields, retorna um objeto de validação de todos os campos obrigatórios da requisição.
     * @param array $valid_field_names Array com o nome dos campos obrigatórios.
     * @param array $request_fields Objeto da requisição a ser validado.
     * @return array Retorna objeto no padrão: ['invalid_fields' => $invalid_fields, 'valid_data' => true | false]
     */
    public function validate_required_fields($valid_field_names, $request_fields)
    {
        $invalid_fields = [];
        $valid_data = true;

        foreach ($valid_field_names as $field_name) {
            if (!empty($request_fields)) {
                if (!array_key_exists($field_name, $request_fields)) {
                    array_push($invalid_fields, "$field_name não enviado");
                    $valid_data = false;
                }
            } else {
                array_push($invalid_fields, "$field_name não enviado");
                $valid_data = false;
            }
        }

        if ($valid_data) {
            foreach ($request_fields as $campo => $valorCampo) {
                if (array_key_exists($campo, $valid_field_names)) {
                    if (is_string($valorCampo)) {
                        if (empty($valorCampo)) {
                            array_push($invalid_fields, "$campo vazio");
                            $valid_data = false;
                        }
                    }
                }
            }
        }


        //return ['invalid_fields' => $invalid_fields, 'valid_data' => $valid_data];
        return ['invalid_fields' => $invalid_fields, 'valid_data' => $valid_data];
    }



    /**
     * Função validate_field_value, retorna uma string em caso do campo não estar de acordo com a validação.
     * @param array $request_fields Objeto recebido com os campos da requisição.
     * @param string $field_name Nome do campo a ser validado.
     * @param string $validation_type Tipo da validação a ser realizada, pode ser: number, int, string, array, date, datetime, uuid, booleano ou custom.
     * @param callable $custom_function Função customizada que vai realizar a validação do valor do campo, deve retornar um objeto no formato: ['valid' => True ou False, 'message' => string].
     * @return string
     */
    public function validate_field_value($request_fields, $field_name, $validation_type, $custom_function = null)
    {
        $invalid_field_str = "";

        if (!empty($request_fields) && !empty($field_name) && !empty($validation_type)) {
            if (array_key_exists($field_name, $request_fields)) {
                $field_value = $request_fields[$field_name];

                switch ($validation_type) {
                    case 'number':
                        if (!is_numeric($field_value)) {
                            $invalid_field_str = "$field_name não é um número";
                        }
                        break;
                    case 'int':
                        if (!is_integer($field_value)) {
                            $invalid_field_str = "$field_name não é um número inteiro";
                        }
                        break;
                    case 'string':
                        if (!is_string($field_value)) {
                            $invalid_field_str = "$field_name não é uma string";
                        }
                        break;
                    case 'array':
                        if (!is_array($field_value)) {
                            $invalid_field_str = "$field_name não é um array";
                        }
                        break;
                    case 'date':
                        $pattern = '/^\d{4}-\d{2}-\d{2}$/';
                        if (!preg_match($pattern, $field_value)) {
                            $invalid_field_str = "$field_name não está no formato YYYY-MM-DD";
                        } else {
                            list($year, $month, $day) = explode('-', $field_value);
                            if (!checkdate($month, $day, $year)) {
                                $invalid_field_str = "$field_name não é uma data válida";
                            }
                        }
                        break;
                    case 'datetime':
                        $pattern = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
                        if (!preg_match($pattern, $field_value)) {
                            $invalid_field_str = "$field_name não está no formato YYYY-MM-DD HH:MM:SS";
                        } else {
                            list($date_part, $time_part) = explode(' ', $field_value);
                            list($year, $month, $day) = explode('-', $date_part);

                            if (!checkdate((int)$month, (int)$day, (int)$year)) {
                                $invalid_field_str = "$field_name não é uma data válida";
                            } else {

                                list($hour, $minute, $second) = explode(':', $time_part);
                                if (
                                    (int)$hour < 0 || (int)$hour > 23 ||
                                    (int)$minute < 0 || (int)$minute > 59 ||
                                    (int)$second < 0 || (int)$second > 59
                                ) {
                                    $invalid_field_str = "$field_name contém uma hora inválida";
                                }
                            }
                        }
                        break;
                    case 'uuid':
                        if (is_string($field_value)) {
                            if (empty($field_value)) {
                                $invalid_field_str = "$field_name vazio";
                            }

                            if (strlen($field_value) != 36) {
                                $invalid_field_str = "$field_name não é um uuid valido";
                            }
                        } else {
                            $invalid_field_str = "$field_name não é uma string";
                        }
                        break;
                    case 'boolean':
                        if (is_bool($field_value)) {
                            if (empty($field_value) && $field_value != false) {
                                $invalid_field_str = "$field_name vazio";
                            }
                        } else {
                            $invalid_field_str = "$field_name não é um booleano";
                        }
                        break;
                    default:
                        if (!empty($custom_function) && is_callable($custom_function)) {
                            $return_validation = call_user_func($custom_function, $field_value, $request_fields);

                            if (is_array($return_validation)) {
                                if (array_key_exists('valid', $return_validation) && array_key_exists('message', $return_validation)) {
                                    if ($return_validation['valid'] == false) {
                                        $message = $return_validation['message'];
                                        if ($message) {
                                            $invalid_field_str = "$field_name $message";
                                        } else {
                                            $invalid_field_str = "$field_name valor inválido";
                                        }
                                    }
                                }
                            }
                        }
                        break;
                }
            } else {
                $invalid_field_str = "$field_name não enviado";
            }
        }

        return $invalid_field_str;
    }

    public function insert_valid_string($string, $array)
    {
        if (is_string($string) && !empty($string)) {
            array_push($array, $string);
        }

        return $array;
    }

    public function prepare_string_invalid_fields($invalid_fields)
    {
        $invalid_fields_str = "";

        if (!empty($invalid_fields)) {
            foreach ($invalid_fields as $index => $campo) {
                if ($index == 0) {
                    $invalid_fields_str = $campo;
                } else {
                    $invalid_fields_str .= ", $campo";
                }
            }
        }

        return $invalid_fields_str;
    }

    /**
     * Função validate_data_execution, valida e retorna um cabeçalho http de erro caso haja um erro com a execução da query
     * @param mixed $execution_response Retorno da execucao da query, seja ele sucesso retorna um objeto com o status QueryHasRun
     * @return array['query_has_run']
     */
    public function validate_data_execution($execution_response)
    {
        if (is_array($execution_response) && array_key_exists("id", $execution_response)) {
            if ($execution_response['id'] != '1') {
                return ['query_has_run' => false, 'throw_error' => HttpErrors::code400('Erro de banco de dados: ' . $execution_response['message'] . '')];
            } else {
                return ['query_has_run' => true];
            }
        } else {
            return ['query_has_run' => true];
        }
    }

    /**
     * Função retornarNumeroSemanaMes, retorna o número da semana baseado no mês (Considerando domingo como o primeiro dia da semana).
     * @param string $data String de data no formato (YYYY-MM-DD).
     * @return string
     */
    public function retornarNumeroSemanaMes($data)
    {
        list($ano, $mes, $dia) = explode('-', $data);
        $primeiroDiaMes = "$ano-$mes-01";

        return $this->retornarNumeroSemanaAno($data) - $this->retornarNumeroSemanaAno($primeiroDiaMes) + 1;
    }

    /**
     * Função retornarNumeroSemanaAno, retorna o número da semana no ano (Considerando domingo como o primeiro dia da semana).
     * @param string $data String de data no formato (YYYY-MM-DD).
     * @return string
     */
    public function retornarNumeroSemanaAno($data)
    {
        $data = new DateTime($data);
        $diaSemana = $data->format("N");
        $numeroSemana = $data->format("W");

        // No php, domingo é o último dia da semana
        if ($diaSemana == 7) {
            $numeroSemana = $numeroSemana + 1;
        }

        return $numeroSemana;
    }

    /**
     * Função retornarUltimoDiaMes, retorna o último dia do mês da data passada como parâmetro.
     * @param string $data String de data no formato (YYYY-MM-DD).
     * @return string
     */
    public function retornarUltimoDiaMes($data)
    {
        $data = new DateTime($data);

        $data->modify('last day of this month');

        return $data->format('Y-m-d');
    }

    /**
     * Função retornarDataSubtraindoMeses, retorna a data do resultado da subtração entre a data informada e o número de meses informado.
     * @param string $data String de data no formato (YYYY-MM-DD).
     * @param int $meses Número de meses a serem subtraídos da data informada.
     * @return string
     */
    public function retornarDataSubtraindoMeses($data, $meses)
    {
        // Criar um objeto DateTime a partir da data fornecida
        $dataObj = DateTime::createFromFormat('Y-m-d', $data);

        // Verificar se a data é válida
        if (!$dataObj) {
            return "Formato de data inválido.";
        }

        // Subtrair os meses especificados
        $dataObj->modify("-$meses months");

        // Retornar a data no formato desejado
        return $dataObj->format('Y-m-d');
    }

    /**
     * Função retornarDataSubtraindoSemanas, retorna a data do resultado da subtração entre a data informada e o número de semanas informado.
     * @param string $data String de data no formato (YYYY-MM-DD).
     * @param int $semanas Número de semanas a serem subtraídas da data informada.
     * @return string
     */
    public function retornarDataSubtraindoSemanas($data, $semanas)
    {
        $dataObj = DateTime::createFromFormat('Y-m-d', $data);

        if (!$dataObj) {
            return "Formato de data inválido.";
        }

        $dataObj->modify('sunday last week');

        $dataObj->modify("-$semanas weeks");

        return $dataObj->format('Y-m-d');
    }

    /**
     * Função verifyQueryResult, retorna se a execução da query encontrou algum problema.
     * @param array $queryResult Resultado da query executada.
     * @return bool
     */
    public function validQueryResult($queryResult)
    {
        if (array_key_exists('valid', $queryResult)) {
            if ($queryResult['valid'] == false) {
                return false;
            }
        }
        return true;
    }
}
