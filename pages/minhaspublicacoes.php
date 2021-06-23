<?php

include 'template.php';

$imagempost = '';

//botão para excluir a publicação
if(isset($_POST['excluir'])){    
    $id = $_POST["excluir"] ?? '';
    excluirpublicacao($id);    
    $id = $_POST["idpublicacao"] ?? '';
}
//botão para editar a publicação
if(isset($_POST['editar'])){    
    $idpostagem = $_POST["editar"] ?? '';
    $mensagem = $_POST["mensagem"] ?? '';
    $titulo = $_POST["titulo"] ?? '';
    $foto = $_FILES['foto'];
    $imagem = $_POST['imagem'] ?? '';
    if ($foto['name'] !== '') {
        $nameImagem = md5($foto['name'] . rand(0,9999));
        $tipo = substr($foto['name'], -4);
        $nomeimagem = "{$nameImagem}{$tipo}";
    
        $imagem = $foto['tmp_name'];
    
        move_uploaded_file($imagem,"img/publicacoes/{$nomeimagem}");
    } else {
        $nameImagem = $imagem;
    }
    editarpostagem($idpostagem,$titulo,$mensagem,$nameImagem); 
    $id = $_POST["idpublicacao"] ?? '';  
}



/**
 * Edita os dados no banco de dados da tabela publicacao
 *
 * @param string $idpostagem
 * @param string $titulo
 * @param string $mensagem
 * @param string $nameImagem
 * @return void
 */
function editarpostagem($idpostagem,$titulo,$mensagem,$nameImagem){
    $connection = connection();
    $sql = "update publicacao set titulo = :titulo,
                                  mensagem = :mensagem,
                                  imagempost = :imagempost  
                                  where id = :id";
    $result = $connection->prepare($sql);
    $result->bindValue(':id',$idpostagem);
    $result->bindValue(':titulo',$titulo);  
    $result->bindValue(':mensagem',$mensagem);  
    $result->bindValue(':imagempost',$nameImagem);  
    $result->execute();
}

/**
 * Exclui a publicacao no banco de dados da tabela publicacao
 *
 * @param string $id
 * @return void
 */
function excluirpublicacao($id){            
    $connection = connection(); 
    $sql = "delete from publicacao where id = :id";
    $result = $connection->prepare($sql);
    $result->bindValue(':id',$id);
    $result->execute();
}


/**
 * Busca todas as publicações feita pelo usuário logado e as retorna em html com opção de ver, editar e exluir cada uma
 *
 * @param string $id
 * @return string
 */
function pubuser($id){
    global $dados;    
    $html = '';
    $connection = connection();
    $sql = 'select * from publicacao order by id desc';
    $result = $connection->prepare($sql);    
    $result->execute();
    $publicacoes = $result->fetchAll(PDO::FETCH_OBJ);   
    
    
    foreach ($publicacoes as $publicacao) {
        if($id == $publicacao->idusuario or $dados['id'] == '1'){
        $mensagem = substr($publicacao->mensagem,1,110);
        $html .= "
                <div class='modal fade' id='excluir-$publicacao->id' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                    <div class='modal-dialog modal-dialog-centered'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                        <h5 class='modal-title' id='exampleModalLabel'>Excluir publicação</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <form method='POST' action='minhaspublicacoes'>
                        <div class='modal-body'>
                        <div class='mb-3'>
                             Deseja <b>excluir</b> a publicação \"$publicacao->titulo\" ?                                            
                        </div>                                            
                        </div>
                        <p class='text-muted mx-3 fs-6 fst-italic fw-light'>$publicacao->datahora</p>
                        <div class='modal-footer'>                  
                        <button type='submit' value='{$publicacao->id}' name='excluir' class='btn btn-danger'>Excluir</button>
                        <button type='button' class='btn btn-primary' data-bs-dismiss='modal'>Cancelar</button>
                        </div>
                        </form>
                    </div>
                    </div>
                </div>

                <div class='modal fade' id='editar-$publicacao->id' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                    <div class='modal-dialog modal-dialog-centered'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                        <h5 class='modal-title' id='exampleModalLabel'>Alterar publicação</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <form method='POST' action='minhaspublicacoes' enctype='multipart/form-data' class='p-3'>
                        <div class='modal-body'>
                        <div class='mb-3'>                        
                        <div class='row mb-3'>
                           <div class='mb-4'>
                             <label for='formFile' class='form-label mx-2 mb-3'>Carregar imagem da publicação</label>
                             <input class='form-control' name='foto' value='$publicacao->imagempost.png' accept='image/png' type='file' id='formFile'>
                           </div>
                           <div class='form-floating mb-4'>
                             <input type='text' class='form-control' id='floatingInput' name='titulo' value='$publicacao->titulo' placeholder='Digite o título..'
                               required>
                             <label for='floatingInput' class='px-4'>Digite o título da publicação</label>
                           </div>
                           <div class='form-floating mb-4'>
                             <textarea class='form-control mt-' placeholder='Escreva sua mensagem...' name='mensagem' id='floatingTextarea'
                               style='height: 100px' required>$publicacao->mensagem</textarea>
                             <label class='px-4' for='floatingTextarea'>Deixe sua mensagem...</label>
                           </div>                                
                        </div>                                                                  
                        </div>                                            
                        </div>
                        <p class='text-muted mx-3 fs-6 fst-italic fw-light'>$publicacao->datahora</p>
                        <div class='modal-footer'>
                        <input name='imagem' value='{$publicacao->imagempost}' style='display: none;'>                  
                        <button type='submit' value='{$publicacao->id}' name='editar' class='btn btn-warning'>Alterar publicação</button>
                        <button type='button' class='btn btn-danger' data-bs-dismiss='modal'>Cancelar</button>
                        </div>
                        </form>
                    </div>
                    </div>
                </div>  

                <div class='col-12 mb-5 p-4 bg-secondary rounded-3 text-light'> 
                    <div class='pb-3 row'>
                        <div class='col-5'>
                            <img src='img/avatares/{$publicacao->avatar}.png' alt='mdo' width='32' height='32' class='rounded-circle mx-2'>
                            <strong class='text-light'>{$publicacao->nome} {$publicacao->sobrenome}</strong>
                        </div>
                        <div class='col-6 fs-6 text-end mx-4'><i>{$publicacao->datahora}</i>
                        </div>
                    </div>
                    <div class='card bg-dark'>
                        <img src='img/publicacoes/{$publicacao->imagempost}.png' width='566px' height='400' class='card-img-top p-1' alt='...' style='height: 350px;'>
                            <div class='card-body'>
                                <h5 class='card-title'>{$publicacao->titulo}</h5>
                                <p class='card-text'>{$mensagem}...</p>
                            </div>
                            <div class='d-grid gap-2 d-flex justify-content-center mb-3'>
                                <form method='POST' action='comentar'> 
                                    <input name='donopub' value='{$publicacao->idusuario}' style='display: none;'>
                                    <button class='btn btn-primary' name='publicacao' value='{$publicacao->id}' type='submit'>Ver publicação</button> 
                                </form> 
                                <form method='POST' action='minhaspublicacoes'> 
                                    <button class='btn btn-warning mx-1' type='button' data-bs-toggle='modal' data-bs-target='#editar-$publicacao->id'>Editar</button>
                                    <button class='btn btn-danger mx-1' type='button' data-bs-toggle='modal' data-bs-target='#excluir-$publicacao->id'>Excluir</button>
                                </form>
                            </div>
                    </div>
                </div>";
        }
    }
    return $html;
}  

/**
 * Verifica se existe alguma publicação feita pelo usuário conectado, caso conectado as exibe, caso não retorna html sugerindo criar uma nova publicação
 *
 * @return string
 */
function minhaspub(){
    global $dados;
    if(pubuser($dados['id']) == ''){
        $retorno =  "<div class='card text-center mt-5'>                                    
                        <div class='card-body'>
                            <h5 class='card-title'>Nenhuma publicação encontrada!</h5>
                            <p class='card-text'>Clique no botão abaixo e crie uma!</p>
                            <a type='button' class='btn btn-primary' href='novapublicacao'>Criar publicação...</a>                                        
                        </div>                                    
                    </div>"; 
    }else{
        $retorno = pubuser($dados['id']); 
    }
    return $retorno;
}

/**
 * Coleta os dados das tags que serão substituídas e retorna a pagina renderizada
 *
 * @return string
 */
function page_minhaspub () {   
    $tags = [
        'header'    => render('pages-html/header.html', $tag = [ "nav" => verificarlogin()]),
        'minhaspub'   => minhaspub(),
        'footer'    => render('pages-html/footer.html')
    ];

    return render('pages-html/minhaspub.html', $tags);
}

echo page_minhaspub();