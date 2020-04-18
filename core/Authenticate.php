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
        
        if(Session::get('adm')){
            return Redirect::route('/adm');
        }elseif ( $validando_sessao != false) {
                        
            //carrega sessao do usuario
            $adm =[
                'idAdm'         => $validando_sessao['idAdm'],
                'nome'          => $validando_sessao['nome'],
                'email'         => $validando_sessao['email'],
                'telefone'      => $validando_sessao['telefone'],
                'observacoes'   => $validando_sessao['observacoes'],
                'created_at'    => $validando_sessao['created_at'],
                'perfil_master' => $validando_sessao['perfil_master']
            ];
            Session::set('adm', $adm);
            self::salva_login($validando_sessao['idAdm']);
            
            return Redirect::route('/adm');
        }else{
            return Redirect::route('/to_login');
        }
    }
    
    
    //Fazer o login caso ja nao esteja logado
    public function to_login()
    {
        $this->setPageTitle('Login');
        return $this->renderView('user/login_anim', 'layout_login');
    }
    
    
    //recebe dados do usuario e tenta autenticar
    public function auth($request)
    {
        //Dados obrigatorios
        $array = [
            "case" => "selecionaadmin",
            "email" => $request->post->email,
        ];

        $resultado_busca = User::funcao_selecionar($array);
        
        $senha_digitada =   $request->post->senha;
        $senha_salva    =   $resultado_busca['resultado'][0]['senha'];

        if ($resultado_busca['erro']){
            return Redirect::route('/to_login', [
                'errors' => ['Usuário ou senha estão incorretos'],
                'inputs' => ['email' => $request->post->email]
            ]);
        }else if((Bcrypt::check($senha_digitada, $senha_salva))){
            
            //Verifica se senha foi resetada
            if($senha_digitada == "123456"){
                return Redirect::route('/login/' . $resultado_busca['resultado'][0]['idAdm'] . '/cadastra_nova_senha');
            }
            
            self::salva_login($resultado_busca['resultado'][0]['idAdm']);
            
            //Verifica se esta ativo
            if($resultado_busca['resultado'][0]['ativo'] == 1){
                
                //carrega sessao do usuario
                $adm =[
                    'idAdm'         => $resultado_busca['resultado'][0]['idAdm'],
                    'nome'          => $resultado_busca['resultado'][0]['nome'],
                    'email'         => $resultado_busca['resultado'][0]['email'],
                    'telefone'      => $resultado_busca['resultado'][0]['telefone'],
                    'observacoes'   => $resultado_busca['resultado'][0]['observacoes'],
                    'created_at'    => $resultado_busca['resultado'][0]['created_at'],
                    'perfil_master' => $resultado_busca['resultado'][0]['perfil_master']
                ];
                Session::set('adm', $adm);
                
                //Verifica Perfil
                $case = $resultado_busca['resultado'][0]['perfil_master'];

                switch ($case):
                    
                    //Adm mais basico
                    case 0:
                        // Verifica LEMBRAR
                        if($request->post->lembrar){
                            self::criar_cookie($resultado_busca['resultado'][0]['idAdm']);
                        }
                        return Redirect::route('/adm');
                        
                    break;
                    
                    //Adm com alguns poderes
                    case 1:
                        echo "perfil 1";
                    break;
                
                    //Adm com Todos os poderes
                    case 2:
                        echo "perfil 2";
                    break;

                    case 3:
                        echo "perfil 3";
                    break;
                
                    case 4:
                        echo "perfil 4";
                    break;
                
                    case 5:
                        echo "perfil 5";
                    break;
                
                    case 6:
                        echo "perfil 6";
                    break;
                
                    case 7:
                         // Verifica LEMBRAR
                        if($request->post->lembrar){
                            self::criar_cookie($resultado_busca['resultado'][0]['idAdm']);
                        }
                        return Redirect::route('/adm_master');
                    break;
                
                    default:
                        return Redirect::route('/to_login', [
                            'errors' => ['Erro de Cadastro. Contate Administrador.'],
                            'inputs' => ['email' => $request->post->email]
                        ]);
                endswitch;

            }else{
                return Redirect::route('/to_login', [
                    'errors' => ['Usuário Inativo. Contate Administrador.'],
                    'inputs' => ['email' => $request->post->email]
                ]);
            }
            
            
        }else{
            return Redirect::route('/to_login', [
                'errors' => ['Usuário ou senha estão incorretos'],
                'inputs' => ['email' => $request->post->email]
            ]);
        }
    }
    
    
    public static function criar_cookie($idAdm)
    {
        // Encriptando o cookie
        date_default_timezone_set('America/Sao_Paulo');
        $data = date('d/m/Y');
        $hora = date('H:i:s');
        
        $senha = 'cookie_objetivando' . $idAdm. $data . $hora;
        $hash = Bcrypt::hash($senha);
        
        $separador = "&@^!";
        $hash_cookie = $idAdm . $separador . $hash;
        
        // Cria o cookie acima só que irá durar 10 dias
        setcookie('objetivando_sessao', $hash_cookie, time() + (10 * 24 * 3600), '/');
        
        //#### Salvar Senha no Banco     
        //Dados obrigatorios
        $array = [
            "case"          => "altera_token_login_adm",
            "idAdm"         => $idAdm,
            "login_token"   => $senha
        ];

        User::funcao_alterar($array);
    }
    
    
    public static function valida_sessao_login()
    {
        $cookie_objetivando = $_COOKIE['objetivando_sessao'];
        
        $separador = "&@^!";
        $id_hash = explode($separador, $cookie_objetivando);

        $id = $id_hash[0];  // ID do USUARIO
        $hash = $id_hash[1]; // Hard de login criptografado

        //Dados obrigatorios
        $array = [
            "case" => "selecionaadmincomid",
            "id" => $id,
        ];

        if($cookie_objetivando == NULL){
            $resultado_busca['erro'] = true;
        }else{
            $resultado_busca = User::funcao_selecionar($array);
        }
        
        if ($resultado_busca['erro'] == true){
           return false; 
        }else if ((crypt($resultado_busca['resultado'][0]['login_token'], $hash) == $hash)){
            return $resultado_busca['resultado'][0];
        }else{
            return false;
        }
    }
    


    public function logout()
    {
        setcookie('objetivando_sessao', '', (time() - 1));
        Session::destroy('adm');

        return Redirect::route('/');
    }
    
    
    //Gravar um login
    public static function salva_login($id__adm)
    {
        //Dados obrigatorios
        $array = [
            "case" => "salva_login_adm",
            "fk_idAdm" => $id__adm,
        ];
        User::funcao_cadastrar($array);
    }
    
    

}