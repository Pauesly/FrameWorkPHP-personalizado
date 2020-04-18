<?php
namespace Core\Web_service;

 

    // Grava registros
    function DBCreate($table, array $data, $insertId = false){
        
        $data = DBEscape($data);
        
        $fields = implode (', ', array_keys($data));
        $values = "'".implode ("', '", $data)."'";

        $query = "INSERT INTO {$table} ( {$fields} ) VALUES ( {$values} )";
        
        return DBExecute($query, $insertId);
//    var_dump($query);
 
    }
    
    

    // Executa Querys
    function DBExecute($query, $insertId = false){ 
        
        $link = DBconnect();
        $result = @mysqli_query($link, $query) or die (mysqli_error($link));
        
        if($insertId)
	        $result = mysqli_insert_id($link);
        
        DBClose($link);
        return $result;
        
    }

?>