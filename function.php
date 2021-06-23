<?php
    
date_default_timezone_set('America/Sao_Paulo');
$dados = $_SESSION["usuario"] ?? ['id' => 'anonimo'];   
$errologin = $errologin ?? '';
 

/**
 * Cria uma conexão com o banco de dados
 *
 * @return string
 */
function connection () {
    global $host,$port,$db,$user,$passwd;
        
    $connection = new PDO("mysql:host={$host};port={$port};dbname={$db}",$user,$passwd);
        
    return $connection;
        
}
 

function verificarlogin() {    
    $dados = $_SESSION["usuario"] ?? ['id' => 'anonimo'];
    if ($_SESSION['acesso'] == false){
        $nav = "
                <div class='text-end'>
                <a type='button' class='btn btn-outline-light me-2' href='login'>Login</a>
                <a type='button' class='btn btn-warning' href='cadastro'>Cadastre-se</a>
                </div>";
      
        } else {
            $nome = ucfirst($dados['nome']);
            $sobrenome = ucfirst($dados['sobrenome']);
            $avatar = $dados['avatar'];
            $nav = "
                    <div class='dropdown text-end'>
                        <a href='#' class='d-block link-light text-decoration-none dropdown-toggle' id='dropdownUser1' data-bs-toggle='dropdown' aria-expanded='false'>
                            <strong class='text-light'>{$nome} {$sobrenome}</strong>
                            <img src='img/avatares/{$avatar}.png' alt='mdo' width='32' height='32' class='rounded-circle mx-2'>
                        </a>
                        <ul class='dropdown-menu text-small' aria-labelledby='dropdownUser1'>
                            <li><a class='dropdown-item' href='novapublicacao'>Nova publicação</a></li>
                            <li><a class='dropdown-item' href='minhaspublicacoes'>Minhas publicações</a></li>
                            <li><a class='dropdown-item' href='perfil'>Perfil</a></li>
                            <li><hr class='dropdown-divider'></li>
                            <li><a class='dropdown-item' href='logout'>Desconectar</a></li>
                        </ul>
                    </div>";
         }
    Return $nav;
}


/**
 * Exclui o usuário da tabela usuarios
 *
 * @param string $id
 * @return void
 */ 
function excluirusuario($id){    
    $connection = connection();
    $sql = "delete from usuarios where id = :id";
    $result = $connection->prepare($sql);
    $result->bindValue(':id',$id);
    $result->execute();
} 


/**
 * Lê as informações do usuário logado e armazena em uma variável
 *
 * @param string $emailacesso
 * @return array
 */
function lerusuario($emailacesso) {
        $connection = connection();
        $sql = "select * from usuarios";
        $result = $connection->prepare($sql);
        $result->execute();
        $clientes = $result->fetchAll(PDO::FETCH_ASSOC);
        foreach ($clientes as $key => $value) {
            if($value["email"] == $emailacesso){
                $_SESSION['usuario'] = ['id' => $value["id"],
                                        'nome' => $value["nome"],
                                        'sobrenome' => $value["sobrenome"],
                                        'email' => $value["email"],
                                        'cidade' => $value["cidade"],
                                        'estado' => $value["estado"],
                                        'avatar' => $value["avatar"],
                                        'senha' =>  $value["senha"]
            ];
        }
    }
    $dados = $_SESSION["usuario"] ?? ['id' => 'anonimo'];
    return $dados;
}


/**
 * Verifica se a senha digitada é a mesma do banco de dados
 *
 * @param string $senha
 * @param string $senhabanco
 * @return void
 */
function verificarusuario($senha,$senhabanco){    
    
    if (password_verify($senha,$senhabanco) == true){
        return true;              
    }
}


/**
 * Busca no banco de dados todas as publicações da tabela publicacoes e retorna as informações no html
 *
 * @return string
 */
function publicacao(){
    $html = '';
    $connection = connection();
    $sql = 'select * from publicacao order by id desc';
    $result = $connection->prepare($sql);
    $result->execute();
    $publicacoes = $result->fetchAll(PDO::FETCH_OBJ);   
    
    
    foreach ($publicacoes as $publicacao) {
        $mensagem = substr($publicacao->mensagem,0,110);
        $html .=    "<div class='col-12 mb-4 p-3 pb-2 bg-dark rounded-3 text-light shadow'> 
                        <div class='pb-3'>                
                            <img src='img/avatares/{$publicacao->avatar}.png' alt='mdo' width='32' height='32' class='rounded-circle mx-2'>
                            <strong class='text-light'>{$publicacao->nome} {$publicacao->sobrenome}</strong>                              
                        </div>
                        <div class='card bg-secondary mx-3 text-dark'>
                            <img src='img/publicacoes/{$publicacao->imagempost}.png' width='425px' height='300' class='card-img-top p-1'>
                                <div class='card-body'>
                                    <h5 class='card-title'>{$publicacao->titulo}</h5>
                                    <p class='card-text'>{$mensagem}...</p>
                                </div>
                                <div class='d-grid gap-2 d-md-flex justify-content-end'>
                                    <form method='POST' action='comentar'> 
                                        <input name='donopub' value='{$publicacao->idusuario}' style='display: none;'>
                                        <button class='btn btn-dark m-3' name='publicacao' value='{$publicacao->id}' type='submit'>Ver publicação</button>  
                                    </form>
                                </div>
                        </div>
                        <div class='col-6 fs-6 p-2'><i>{$publicacao->datahora}</i></div>
                    </div>";
    }
    return $html;
}  


/**
 * Busca no banco de dados todas as mensagens da tabela comentarios e retorna as informações em html
 *
 * @param string $id
 * @return string
 */
function mensagem($id){
        $postagem = '';
        $connection = connection();
        $sql = 'select * from publicacao';
        $result = $connection->prepare($sql);
        $result->execute();
        $publicacoes = $result->fetchAll(PDO::FETCH_OBJ);
        
        
        foreach ($publicacoes as $publicacao) {
            if($publicacao->id == $id){
            $postagem = "<div class=' p-4 bg-dark rounded-3 shadow'> 
                            <div class='pb-3 row'>
                                <div class='col-6'>
                                    <img src='img/avatares/{$publicacao->avatar}.png' alt='mdo' width='32' height='32' class='rounded-circle mx-2'>
                                    <strong class='text-light'>{$publicacao->nome} {$publicacao->sobrenome}</strong>
                                </div>
                                <div class='col-6 fs-6 text-light text-end px-4'><i>{$publicacao->datahora}</i></div>
                                </div>
                                <div class='card bg-secondary'>
                                <img src='img/publicacoes/{$publicacao->imagempost}.png' width='425px' height='300' class='card-img-top p-1'>
                                    <div class='card-body'>
                                    <h5 class='card-title' >{$publicacao->titulo}</h5>
                                    <div class='overflow-auto' style='height: 20vh;'>
                                    <p class='card-text'>{$publicacao->mensagem}</p>
                                    </div>
                                </div>                  
                            </div>
                         </div>";
        }   
    }
    return $postagem;
}  


/**
 * Verifica se existe alguma publicacao no banco de dados na tabela publicacao, caso não exista retorna html sugerindo criar uma nova publicação
 *
 * @return string
 */
function verificarpublicacao() {

if(publicacao() == ''){
    global $dados;
    $retorno = "<div class='card text-center col-8 mt-5'>                                    
                    <div class='card-body'>
                        <h5 class='card-title'>Nenhuma publicação encontrada!</h5>
                            <p class='card-text'>Seja o primeiro a criar uma!</p>
                        <a type='button' class='btn btn-primary' href='novapublicacao'>Criar publicação...</a>                                        
                    </div>                                    
                </div>"; 
  }else{
  $retorno = publicacao(); 
    }
  return $retorno;
}


/**
 * Verifica se o $email já está cadastrado no banco de dados na tabela usuarios
 *
 * @param string $email
 * @return boolean
 */
function verificaremail($email){
    $retorno = false;
    $connection = connection();
    $sql = "select * from usuarios";
    $result = $connection->prepare($sql);
    $result->execute();
    $clientes = $result->fetchAll(PDO::FETCH_ASSOC);

    foreach ($clientes as $key => $value) {
        if($value["email"] == $email){
            $retorno = true;
        } 
    }
return $retorno;
}
