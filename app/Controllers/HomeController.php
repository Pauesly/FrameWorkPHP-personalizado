<?php

namespace App\Controllers;

use Core\BaseController;
use Core\Redirect;
use Core\Validator;
use App\Models\Email;

class HomeController extends BaseController
{
    
    private $dados;

    private $email;

    public function __construct()
    {
        parent::__construct();
       // $this->email = new Email;
    }
    
    
    public function index(){
        $this->setPageTitle('Home');
        $this->renderView('home/index', 'layout_main');
    }

    
    
    
}