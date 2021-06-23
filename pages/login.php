<?php

include 'template.php';

$_SESSION['acesso'] = false;
$errologin = "";


if(isset($_POST['btnentrar'])){
        $emailacesso = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        if($emailacesso == '' or $senha == ''){
            $errologin = "<div class='alert alert-danger text-center mx-auto' style='width: 400px;'>Preencha todos os campos!</div>";
        } else {
            $dados = lerusuario($emailacesso);
            $senhabanco = $_SESSION["usuario"]["senha"] ?? "";
        if (verificarusuario($senha,$senhabanco) == true){
            $_SESSION['acesso'] = true;
            header("Location: feed");     
        } else {
            $errologin = "<div class='alert alert-danger text-center mx-auto' style='width: 600px;'>Usuário e senha não cadastrados ou dados incorretos!</div>";  
        };
    }        
}

/**
 * Coleta os dados das tags que serão substituídas e retorna a pagina renderizada
 *
 * @return string
 */
function page_login () {
    global $errologin;
    $tags = [
        'header'    => render('pages-html/header.html', $tag = [ "nav" => verificarlogin()]),
        'errologin'    => $errologin,
        'footer'    => render('pages-html/footer.html')
    ];

    return render('pages-html/login.html', $tags);
}

echo page_login();  