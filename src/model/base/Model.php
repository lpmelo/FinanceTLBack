<?php

namespace Model\Base;

use Exception;
use Modules\Data\MySqlConnector;
use Ramsey\Uuid\Nonstandard\Uuid;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Model
{
    protected $ambiente;
    protected $nomeTabela;
    protected $chavePrimaria;
    protected $camposOcultos;
    protected $mysql;

    public function __construct($nomeTabela, $chavePrimaria, $camposOcultos = null)
    {
        $this->ambiente = $_ENV['AMBIENTE'];
        $this->nomeTabela = $this->ambiente != "planejamento" ? $nomeTabela : "p_$nomeTabela";
        $this->chavePrimaria = $chavePrimaria;
        $this->camposOcultos = $camposOcultos;
        $this->mysql = new MySqlConnector();
    }

    public function generate_access_token($user_data)
    {

        $payload = [
            'iss' => 'Internal',
            'aud' => 'FinanceTL_API',
            'iat' => time(),
            'exp' => time() + (7 * 24 * 60 * 60),
            'data' => $user_data
        ];

        return JWT::encode($payload, $_ENV['JWT_KEY'], 'HS256');
    }

    public function decode_access_token($token)
    {
        try {
            $key = new Key($_ENV['JWT_KEY'], 'HS256');
            $decoded = JWT::decode($token, $key);
            return ['valid' => true, 'decode' => $decoded];
        } catch (Exception $e) {
            return ['valid' => false, 'error' => $e->getMessage()];
        }
    }


    private function LoopEsconderCampo($esconderCamposArray, $resultadosArray)
    {
        if (!empty($esconderCamposArray) && is_array($resultadosArray)) {
            foreach ($resultadosArray as &$registro) {
                if(is_array($registro)){
                    foreach ($esconderCamposArray as $campo) {
                        unset($registro[$campo]);
                    }
                }
            }
        }
        return $resultadosArray;
    }

    /**
     * Função generate_access_token - Gera um JSON Token para as informações da sessão
     * @param mixed $userData 
     * @param mixed $secretKey
     * @return string
     */

    public function hide_fields($resultado, $camposOcultosPersonalizado = null)
    {
        if (!is_null($camposOcultosPersonalizado)) {
            $resultado = $this->LoopEsconderCampo($camposOcultosPersonalizado, $resultado);
        } else {
            $resultado = $this->LoopEsconderCampo($this->camposOcultos, $resultado);
        }

        return $resultado;
    }
}
