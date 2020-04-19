<?php

namespace Core;

use App\Models\User;
use App\Models\Bcrypt;
use Core\Session;

trait Authenticate
{
   
    public function login()
    {
        $validando_sessao = self::valida_sessao_login();
        
        
    }
    
    
    
    

}
