<?php

namespace Core;

class Validator
{
    public static function make(array $data, array $rules)
    {
        $errors = null;

        foreach ($rules as $ruleKey => $ruleValue) {

            foreach ($data as $dataKey => $dataValue) {
                if ($ruleKey == $dataKey) {

                    $itemsValue = [];
                    if(strpos($ruleValue, "|")){
                        $itemsValue = explode("|", $ruleValue);

                        foreach ($itemsValue as $itemValue){
                            $subItems = [];
                            if(strpos($itemValue, ":")){
                                $subItems = explode(":", $itemValue);
                                switch ($subItems[0]) {
                                    case 'min':
                                        if (strlen($dataValue) < $subItems[1])
                                            $errors["$ruleKey"] = "O campo {$ruleKey} deve ter um mínimo de {$subItems[1]} caracteres.";
                                        break;
                                    case 'max' :
                                        if (strlen($dataValue) > $subItems[1])
                                            $errors["$ruleKey"] = "O campo {$ruleKey} deve ter um máximo de {$subItems[1]} caracteres.";
                                        break;
                                    case 'unique' :
                                        $objModel = "\\App\\Models\\" . $subItems[1];
                                        $model = new $objModel;
                                        $find = $model->where($subItems[2], $dataValue)->first();
                                        if($find->$subItems[2]){
                                            if(isset($subItems[3]) && $find->id == $subItems[3]){
                                                break;
                                            }else{
                                                $errors["$ruleKey"] = "{$ruleKey} já registrado no banco de dados.";
                                                break;
                                            }
                                        }
                                        break;
                                }
                            }else{
                                switch ($itemValue) {
                                    case 'required':
                                        if ($dataValue == ' ' || empty($dataValue))
                                            $errors["$ruleKey"] = "O campo {$ruleKey} deve ser preenchido.";
                                        break;

                                    case 'email':
                                        if (!filter_var($dataValue, FILTER_VALIDATE_EMAIL))
                                            $errors["$ruleKey"] = "O campo {$ruleKey} não é válido.";
                                        break;

                                    case 'float':
                                        if (!filter_var($dataValue, FILTER_VALIDATE_FLOAT))
                                            $errors["$ruleKey"] = "O campo {$ruleKey} deve conter número decimal.";
                                        break;

                                    case 'int':
                                        if (!filter_var($dataValue, FILTER_VALIDATE_INT))
                                            $errors["$ruleKey"] = "O campo {$ruleKey} deve conter número inteiro.";
                                        break;
                                    default :
                                        break;
                                }
                            }

                        }

                    }elseif (strpos($ruleValue, ":")) {
                        $items = explode(":", $ruleValue);
                        switch ($items[0]) {
                            case 'min':
                                if (strlen($dataValue) < $items[1])
                                    $errors["$ruleKey"] = "O campo {$ruleKey} deve ter um mínimo de {$items[1]} caracteres.";
                                break;
                            case 'max' :
                                if (strlen($dataValue) > $items[1])
                                    $errors["$ruleKey"] = "O campo {$ruleKey} deve ter um máximo de {$items[1]} caracteres.";
                                break;
                            case 'unique' :
                                $objModel = "\\App\\Models\\" . $subItems[1];
                                $model = new $objModel;
                                $find = $model->where($subItems[2], $dataValue)->first();
                                if($find->$subItems[2]){
                                    if(isset($subItems[3]) && $find->id == $subItems[3]){
                                        break;
                                    }else{
                                        $errors["$ruleKey"] = "{$ruleKey} já registrado no banco de dados.";
                                        break;
                                    }
                                }
                                break;
                        }

                    } else {
                        switch ($ruleValue) {
                            case 'required':
                                if ($dataValue == ' ' || empty($dataValue))
                                    $errors["$ruleKey"] = "O campo {$ruleKey} deve ser preenchido.";
                                break;

                            case 'email':
                                if (!filter_var($dataValue, FILTER_VALIDATE_EMAIL))
                                    $errors["$ruleKey"] = "O campo {$ruleKey} não é válido.";
                                break;

                            case 'float':
                                if (!filter_var($dataValue, FILTER_VALIDATE_FLOAT))
                                    $errors["$ruleKey"] = "O campo {$ruleKey} deve conter número decimal.";
                                break;

                            case 'int':
                                if (!filter_var($dataValue, FILTER_VALIDATE_INT))
                                    $errors["$ruleKey"] = "O campo {$ruleKey} deve conter número inteiro.";
                                break;
                            default :
                                break;
                        }

                    }
                }
            }
        }

        if ($errors) {
            Session::set('errors', $errors);
            Session::set('inputs', $data);
            return true;
        } else {
            Session::destroy(['errors', 'inputs']);
            return false;
        }
    }
    
    
    
    public static function validar_senha($senha){
        
        $min_size = false;
        $valida_numero = false;
        $valida_letra_min = false;
        $valida_letra_mai = false;

        // Valida Tamanho
        if(strlen($senha) >= 6)
        $min_size = true;


        // Valida Numero
        for ($i=0; $i < strlen($senha); $i++){
            if(is_numeric($senha[$i])){
                $valida_numero = true;
            }
        }


        //Valida Letra Minuscula
        $letras_min = "abcdefghijklmnopqrstuvxyzw";
        for ($i = 0; $i < strlen($senha); $i++){
            for($j = 0; $j < strlen($letras_min); $j++){
                if ($senha[$i] == $letras_min[$j]){
                    $valida_letra_min = true;
                }
            }
        }


        // Valida Letra Maiuscula
        $letras_mai = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        for ($i = 0; $i < strlen($senha); $i++){
            for($j = 0; $j < strlen($letras_mai); $j++){
                if ($senha[$i] == $letras_mai[$j]){
                    $valida_letra_mai = true;
                }
            }
        }


        // Verifica se todos os parâmetros foram validados
        if(($min_size) && ($valida_numero) && ($valida_letra_mai || $valida_letra_min) ){
            return true;
        }else{
            return false;
        }
        
    }
    
    
}