<?php

namespace Core;

use App\Models\Web_service;

abstract class BaseModel
{

    
    public static function funcao_alterar($array){
        $caminho = Web_service::ENDERECO;
        $caminho .= Web_service::FUNCAO_ALTERA;
        
        // Carregando dados
        $content = http_build_query($array);
        $context = stream_context_create(array('http' => array('method'  => 'POST', 'content' => $content,)));
        $result = file_get_contents($caminho, null, $context);
        if($result){
            $status[erro] = false;
            // Converts it into a PHP object
            //var_dump($result); // DUMP pra saber qual o erro no comando SQL
            $resultado = json_decode($result, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                exit("Erro ao converter Json: " . json_last_error_msg());
            }
            //$retorno = $resultado['resultado'];
            $retorno = $resultado;
        }
        return($retorno);
    }
    
    
    
    public static function funcao_cadastrar($array){
        $caminho = Web_service::ENDERECO;
        $caminho .= Web_service::FUNCAO_CADASTRA;
        
        // Carregando dados
        $content = http_build_query($array);
        $context = stream_context_create(array('http' => array('method'  => 'POST', 'content' => $content,)));
        $result = file_get_contents($caminho, null, $context);
        if($result){
            $status[erro] = false;
            // Converts it into a PHP object
            //var_dump($result);
            $resultado = json_decode($result, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                exit("Erro ao converter Json: " . json_last_error_msg());
            }
            //$retorno = $resultado['resultado'];
            $retorno = $resultado;
            
        }
        return($retorno);
    }
    
    
    
    public static function funcao_selecionar($array){
        $caminho = Web_service::ENDERECO;
        $caminho .= Web_service::FUNCAO_SELECIONA;
        

        // Carregando dados
        $content = http_build_query($array);
        $context = stream_context_create(array('http' => array('method'  => 'POST', 'content' => $content,)));
        $result = file_get_contents($caminho, null, $context);
        if($result){
            $status['erro'] = false;
            // Converts it into a PHP object
            $resultado = json_decode($result, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                exit ("err" . $result);
                exit("Erro ao converter Json: " . json_last_error_msg());
            }
            //$retorno = $resultado['resultado'];
            $retorno = $resultado;
        }
        return($retorno);
    }

}