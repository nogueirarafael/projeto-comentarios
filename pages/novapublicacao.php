<?php

include 'template.php';

if(isset($_POST['postar'])){    
    $titulo = $_POST["titulo"] ?? '';
    $mensagem = $_POST["mensagem"] ?? '';
    $foto = $_FILES['foto'];
    if($titulo == '' or $mensagem =='' ){
        $erro = "*Preencha todos os campos!";
        $tipoalerta = "alert-danger";
        $erro = " <div class='alert <?php echo {$tipoalerta};?> col-12 mx-auto text-center' style='width: 600px;'>
                <?php echo {$erro};?>
                </div>";
    } else {
    
    if ($foto['name'] !== '') {
        $nameImagem = md5($foto['name'] . rand(0,9999));
        $tipo = substr($foto['name'], -4);
        $nomeimagem = "{$nameImagem}{$tipo}";
    
        $imagem = $foto['tmp_name'];
    
        move_uploaded_file($imagem,"img/publicacoes/{$nomeimagem}");
    } else {
        $nameImagem = "semimagem";
    }
    $nome = $dados["nome"];
    $sobrenome = $dados["sobrenome"];
    $avatar = $dados["avatar"];
    $idusuario = $dados["id"];
    $datahora = "Postado ".date("d/m/Y")." às ".date("H:i:s");
    inserir($idusuario,$titulo,$mensagem,$nome,$sobrenome,$avatar,$nameImagem,$datahora);
    }
}


/**
 * Cria uma nova publicacao no banco de dados na tabela publicacao
 *
 * @param string $idusuario
 * @param string $titulo
 * @param string $mensagem
 * @param string $nome
 * @param string $sobrenome
 * @param string $avatar
 * @param string $nameImagem
 * @param string $datahora
 * @return void
 */
function inserir($idusuario,$titulo,$mensagem,$nome,$sobrenome,$avatar,$nameImagem,$datahora){
    $connection = connection();
    $sql = "insert into publicacao (idusuario,titulo,mensagem,nome,sobrenome,avatar,imagempost,datahora)
    value (:idusuario,:titulo,:mensagem,:nome,:sobrenome,:avatar,:imagempost,:datahora)";
    $result = $connection->prepare($sql); 
    $result->bindValue(':idusuario',$idusuario); 
    $result->bindValue(':titulo',$titulo); 
    $result->bindValue(':mensagem',$mensagem); 
    $result->bindValue(':nome',$nome); 
    $result->bindValue(':sobrenome',$sobrenome); 
    $result->bindValue(':avatar',$avatar); 
    $result->bindValue(':imagempost',$nameImagem); 
    $result->bindValue(':datahora',$datahora); 
    $result->execute(); 
    header("location: feed");
}


/**
 * Verifica se o usuário é anonimo, caso anonimo retorna html pedindo para se conectar ou cadastrar, caso não seja anonimo exibe form html para criar uma publicacao
 *
 * @return string
 */
function novapub(){
    global $dados;
    $dados = $dados ?? ['id' => 'anonimo'];
    if ($dados['id']=='anonimo') {
        $retorno = "<div class='card text-center'>                                    
                        <div class='card-body'>
                            <h5 class='card-title'>Entre ou cadastre-se para criar publicações!</h5>
                                <p class='card-text'>Clique nos botões a baixo para ser redirecionado.</p>
                                    <a type='button' class='btn btn-primary' href='login'>Entrar</a>                                        
                                    <a type='button' class='btn btn-warning' href='cadastro'>Cadastrar</a>                                        
                        </div>                                    
                    </div> ";
      }else{ 
        $retorno = "
                <div class='card-header border-dark text-center text-dark'><b>Nova publicação</b></div>
                    <form method='POST' action='novapublicacao' enctype='multipart/form-data' class='p-3'>
                        <div class='row mb-3'>
                            <div class='mb-4'>
                                <label for='formFile' class='form-label mx-2 mb-3'>Carregar imagem da publicação</label>
                                    <input class='form-control' name='foto' accept='image/png' type='file' id='formFile'>
                            </div>
                            <div class='form-floating mb-4'>
                                    <input type='text' class='form-control' id='floatingInput' name='titulo' placeholder='Digite o título..' required>
                                    <label for='floatingInput' class='px-4'>Digite o título da publicação</label>
                            </div>
                            <div class='form-floating mb-4'>
                                <textarea class='form-control mt-' placeholder='Escreva sua mensagem...' name='mensagem' id='floatingTextarea' style='height: 100px' required></textarea>
                                <label class='px-4' for='floatingTextarea'>Deixe sua mensagem...</label>
                            </div>
                            <div class='d-flex flex-row mb-2 justify-content-center'>
                                <div>
                                    <button type='submit' name='postar' class='btn btn-warning'>Postar</button>
                                </div>
                                <div class='mx-3'>
                                    <a href='feed' name='postar' class='btn btn-danger'>Cancelar</a>
                                </div>
                            </div>
                        </div>
                    </form>";
   } 
   return $retorno;
}


/**
 * Coleta os dados das tags que serão substituídas e retorna a pagina renderizada
 *
 * @return string
 */
function page_novapub () {   
    $tags = [
        'header'    => render('pages-html/header.html', $tag = [ "nav" => verificarlogin()]),
        'novapub'   => novapub(),
        'footer'    => render('pages-html/footer.html')
    ];

    return render('pages-html/novapub.html', $tags);
}

echo page_novapub();