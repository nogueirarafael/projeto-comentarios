<?php

include 'template.php';

if ($dados['id'] == '1'){
    $disabled = 'disabled';
}

if(isset($_POST['alterarsenha'])){ 
    $senhaatual = $_POST["senhaatual"] ?? '';
    $novasenha = $_POST["novasenha"] ?? '';
    $confirmasenha = $_POST["confirmasenha"] ?? '';

    if(verificarusuario($senhaatual,$dados['senha']) == false){
        $erro = "<div class='alert alert-danger text-center mx-auto' style='width: 600px;'>Senha atual inválida, tente novamente</div>";
    } else if($novasenha == $confirmasenha){
            $senhasegura = password_hash($novasenha,PASSWORD_DEFAULT,['cost' =>12]);
            alterarsenha($dados['id'],$senhasegura);
            $erro = "<div class='alert alert-success text-center mx-auto' style='width: 600px;'>Senha alterada com sucesso!</div>";  
        } else {        
            $erro = "<div class='alert alert-danger text-center mx-auto' style='width: 600px;'>Novas senhas não coincidem!</div>";
    }   
    lerusuario($dados['email']);
}
                                        
if(isset($_POST['excluir'])){ 
    $senhaatual = $_POST["senhaatual"] ?? '';
    
    if(verificarusuario($senhaatual,$dados['senha']) == false){
        $erro = "<div class='alert alert-danger text-center mx-auto' style='width: 600px;'>Senha inválida, tente novamente</div>";
    } else {
            excluirusuario($dados['id']);
            $erro = '';
            $logout =  "<div class='text-center mt-5 mx-auto' style='width: 600px;'>
                            <div class='col-auto'>
                            <div class='card'>
                                <div class='card-body'>
                                <h5 class='card-title'>Usuário excluído com sucesso!</h5>
                                <p class='card-text'>Sentiremos saudades :'(</p>
                                <a href='logout' class='btn btn-primary'>ok</a>
                                </div>
                            </div>
                        </div>";
            $display = "style='display: none;'";
            $_SESSION['acesso'] = false;
            $dados = ['id' => 'anonimo'];
    }  
}

if(isset($_POST['editar'])){ 
   
    $nome = $_POST["nome"] ?? '';
    $sobrenome = $_POST["sobrenome"] ?? '';
    $email = $_POST['email'] ?? '';   
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $foto = $_FILES['foto'];    
 
    if ((verificaremail($email) == true) and ($email != $dados['email'])){
        $erro = "<div class='alert alert-danger text-center mx-auto' style='width: 600px;'>E-mail já cadastrado!</div>";        
    } else {    
        if ($foto['name'] !== '') {
            $nameImagem = md5($foto['name'] . rand(0,9999));
            $tipo = substr($foto['name'], -4);
            $nomeimagem = "{$nameImagem}{$tipo}";            
            $imagem = $foto['tmp_name'];
            move_uploaded_file($imagem,"img/avatares/{$nomeimagem}");
        } else { 
            $nameImagem = $dados['avatar'];
        }        
        editarusuario($dados['id'],$nome,$sobrenome,$email,$cidade,$estado,$nameImagem);
        $erro = "<div class='alert alert-success text-center mx-auto' style='width: 600px;'>Dados atualizados com sucesso!</div>";                      
        $dados = lerusuario($email);
    }
}


/**
 * altera as informações do usuário cadastradas no banco de dados na tabela usuários
 *
 * @param string $id
 * @param string $nome
 * @param string $sobrenome
 * @param string $email
 * @param string $cidade
 * @param string $estado
 * @param string $nameImagem
 * @return void
 */
function editarusuario($id,$nome,$sobrenome,$email,$cidade,$estado,$nameImagem){
    $connection = connection();
$sql = "update usuarios set nome = :nome,
                               sobrenome = :sobrenome, 
                               email = :email, 
                               cidade = :cidade, 
                               estado = :estado, 
                               avatar = :avatar 
                               where id = :id";
$result = $connection->prepare($sql);
$result->bindValue(':id',$id);
$result->bindValue(':nome',$nome); 
$result->bindValue(':sobrenome',$sobrenome);  
$result->bindValue(':email',$email);  
$result->bindValue(':cidade',$cidade);  
$result->bindValue(':estado',$estado);  
$result->bindValue(':avatar',$nameImagem);  
$result->execute();
}


/**
 * Altera a senha do usuário solicitado
 *
 * @param string $id
 * @param string $senha
 * @return void
 */
function alterarsenha($id,$senha){    
    $connection = connection();
    $sql = "update usuarios set senha = :senha where id = :id";
    $result = $connection->prepare($sql);
    $result->bindValue(':id',$id);
    $result->bindValue(':senha',$senha); 
    $result->execute();
}  


/**
 * Coleta os dados das tags que serão substituídas e retorna a pagina renderizada
 *
 * @return string
 */
function page_perfil () {
    global $erro, $display, $logout,$disabled,$dados;

    
    $tags = [
        'header'    => render('pages-html/header.html', $tag = [ "nav" => verificarlogin()]),
        'erro'      => $erro,
        'display'   => $display,
        'logout'    => $logout ?? '',
        'nome'      => $dados['nome'] ?? '',
        'sobrenome' => $dados['sobrenome'] ?? '',
        'email'     => $dados['email'] ?? '',
        'cidade'    => $dados['cidade']?? '',
        'estado'    => $dados['estado'] ?? '',
        'avatar'    => $dados['avatar'] ?? '',        
        'disabled'  => $disabled ?? '',        
        'footer'    => render('pages-html/footer.html')
    ];

    return render('pages-html/perfil.html', $tags);
}

echo page_perfil();
