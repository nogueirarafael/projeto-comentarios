<?php

include 'template.php';

$erro="";
$tipoalerta = '';


if(isset($_POST['cadastrar'])){    
    $nome = $_POST["nome"] ?? '';
    $sobrenome = $_POST["sobrenome"] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmasenha = $_POST['confirmasenha'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $foto = $_FILES['foto'];
    $senhasegura = password_hash($senha,PASSWORD_DEFAULT,['cost' =>12]);

    if($nome == '' or $sobrenome =='' or $email == '' or $senha == '' or $cidade == '' or $estado == ''){
        $erro = "*Preencha todos os campos!";
        $tipoalerta = "alert-danger";
        $erro = " <div class='alert <?php echo {$tipoalerta};?> col-12 mx-auto text-center' style='width: 600px;'>
                <?php echo {$erro};?>
                </div>";
    } else if ($senha != $confirmasenha){
        $erro = "*As senhas não coincidem*";
        $tipoalerta = "alert-danger";
        $erro = "<div class='alert {$tipoalerta} col-12 mx-auto text-center' style='width: 600px;'>
                $erro
                </div>";
    } else if (verificaremail($email) == true){
        $erro = "E-mail já cadastrado!";
        $tipoalerta = "alert-danger";
        $erro = "<div class='alert {$tipoalerta} col-12 mx-auto text-center' style='width: 600px;'>
        $erro
        </div>";
    } else {    
            if ($foto['name'] !== '') {
                    $nameImagem = md5($foto['name'] . rand(0,9999));
                    $tipo = substr($foto['name'], -4);
                    $nomeimagem = "{$nameImagem}{$tipo}";            
                    $imagem = $foto['tmp_name'];
                    move_uploaded_file($imagem,"img/avatares/{$nomeimagem}");
            } else { 
                $nameImagem = "semavatar";
            }

            inserircadastro($nome,$sobrenome,$email,$senhasegura,$cidade,$estado,$nameImagem);
            lerusuario($email);
            $_SESSION['acesso'] = true;
            header("location: feed");
    }
}


/**
 * Cria um novo usuário no banco de dados na tabela usuarios
 *
 * @param string $nome
 * @param string $sobrenome
 * @param string $email
 * @param string $senhasegura
 * @param string $cidade
 * @param string $estado
 * @param string $nameImagem
 * @return void
 */
function inserircadastro($nome,$sobrenome,$email,$senhasegura,$cidade,$estado,$nameImagem){
    $connection = connection();
    $sql = "insert into usuarios(nome,sobrenome,email,senha,cidade,estado,avatar)
    value (:nome,:sobrenome,:email,:senha,:cidade,:estado,:avatar)";
    $result = $connection->prepare($sql); 
    $result->bindValue(':nome',$nome); 
    $result->bindValue(':sobrenome',$sobrenome); 
    $result->bindValue(':email',$email); 
    $result->bindValue(':senha',$senhasegura); 
    $result->bindValue(':cidade',$cidade); 
    $result->bindValue(':estado',$estado); 
    $result->bindValue(':avatar',$nameImagem); 
    $result->execute(); 
    $_SESSION['acesso'] = true;
}



/**
 * Coleta os dados das tags que serão substituídas e retorna a pagina renderizada
 *
 * @return string
 */
function page_cadastro () {
    global $erro;
    $tags = [
        'header'    => render('pages-html/header.html', $tag = [ "nav" => verificarlogin()]),
        'erro'      => $erro,
        'footer'    => render('pages-html/footer.html')
    ];

    return render('pages-html/cadastro.html', $tags);
}

echo page_cadastro();
