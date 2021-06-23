<?php

include 'template.php';

$donopub = $_POST['donopub'] ?? '';
$id = '';


//botão cancelar
if(isset($_POST['cancelar'])){    
    $id = $_POST["cancelar"] ?? ''; 
}
//botão do feed de noticias enviando o id da publicação
if(isset($_POST['publicacao'])){    
    $id = $_POST["publicacao"] ?? '';
    $donopub = $_POST['donopub'] ?? '';
}
//botão para postar o comentário
if(isset($_POST['comentar'])){    
    $id = $_POST["comentar"] ?? '';
    $idpublicacao = $_POST["comentar"] ?? '';
    $idusuario = $dados["id"] ?? 'anonimo';
    $comentario = $_POST["comentario"] ?? '';
    $nome = $dados["nome"] ?? 'Anônimo';
    $sobrenome = $dados["sobrenome"] ?? '';
    $avatar = $dados["avatar"] ?? 'anonimo';
    $datahora = date("d/m/Y")." às ".date("H:i:s");
    if ($comentario != ''){
        postarcomentario($idpublicacao,$idusuario,$comentario,$nome,$sobrenome,$avatar,$datahora);              
    }
}

//botão para excluir o comentario
if(isset($_POST['excluir'])){    
    $id = $_POST["excluir"] ?? '';
    excluircomentario($id);    
    $id = $_POST["idpublicacao"] ?? '';
}

//botão para editar o comentario
if(isset($_POST['editar'])){    
    $idcomentario = $_POST["editar"] ?? '';
    $mensagem = $_POST["comentario"] ?? '';
    $datahora = date("d/m/Y")." às ".date("H:i:s")." (editado)";
    editarcomentario($idcomentario,$mensagem,$datahora); 
    $id = $_POST["idpublicacao"] ?? '';   
}

/**
 * Edita as informações do comentário selecionado
 *
 * @param string $idcomentario
 * @param string $comentario
 * @param string $datahora
 * @return void
 */
function editarcomentario($idcomentario,$comentario,$datahora){
    $connection = connection();
$sql = "update comentarios set comentario = :comentario,
                               datahora = :datahora 
                               where id = :id";
$result = $connection->prepare($sql);
$result->bindValue(':id',$idcomentario);
$result->bindValue(':comentario',$comentario); 
$result->bindValue(':datahora',$datahora);  
$result->execute();
}


/**
 * Cria um novo comentário na publicação selecionada
 *
 * @param string $idpublicacao
 * @param string $idusuario
 * @param string $comentario
 * @param string $nome
 * @param string $sobrenome
 * @param string $avatar
 * @param string $datahora
 * @return void
 */
function postarcomentario($idpublicacao,$idusuario,$comentario,$nome,$sobrenome,$avatar,$datahora){
    $connection = connection();
    $sql = "insert into comentarios (idpublicacao,idusuario,comentario,nome,sobrenome,avatar,datahora)
    value (:idpublicacao,:idusuario,:comentario,:nome,:sobrenome,:avatar,:datahora)";
    $result = $connection->prepare($sql); 
    $result->bindValue(':idpublicacao',$idpublicacao); 
    $result->bindValue(':idusuario',$idusuario); 
    $result->bindValue(':comentario',$comentario); 
    $result->bindValue(':nome',$nome); 
    $result->bindValue(':sobrenome',$sobrenome); 
    $result->bindValue(':avatar',$avatar); 
    $result->bindValue(':datahora',$datahora); 
    $result->execute(); 

}


/**
 * Caso não exista comentários no banco de dados retorna mensagem 'sem comentários'
 * Busca todos os comentários da publicação selecionada e os retorna em html
 * Caso usuário conectado seja dono da publicação ou admin exibe botão excluir em todos os comentários
 * Caso usuário seja dono do comentário exibe botão excluir e editar
 * Caso usuário seja anônimo não exibe botão excluir ou editar
 * 
 * @param string $id
 * @return string
 */
function lercomentarios($id){
    global $dados,$donopub,$idusuario;
    $listarcomentarios = '';
    $connection = connection();
    $sql = 'select * from comentarios order by id desc';
    $result = $connection->prepare($sql);
    $result->execute();
    $publicacoes = $result->fetchAll(PDO::FETCH_OBJ);
        
    $dados['id'] = $dados['id'] ?? '';

    foreach ($publicacoes as $publicacao) {
        if($publicacao->idpublicacao == $id){            
            $listarcomentarios .= "
                                    <div class='modal fade' id='comentario-$publicacao->id' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                                        <div class='modal-dialog modal-dialog-centered'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h5 class='modal-title' id='exampleModalLabel'>Editar comentário</h5>
                                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                </div>
                                                <form method='POST' action='comentar'>
                                                    <div class='modal-body'>
                                                        <div class='mb-3'>
                                                            <label for='message-text' class='col-form-label'>Messagem:</label>
                                                            <textarea class='form-control' name='comentario' style='height: 100px;'>$publicacao->comentario</textarea>
                                                        </div>                                            
                                                    </div>
                                                <div class='modal-footer'>
                                                    <input name='idpublicacao' value='{$publicacao->idpublicacao}' style='display: none;'>
                                                        <button type='submit' value='{$publicacao->id}' name='editar' class='btn btn-primary'>Salvar alteração</button>
                                                        <button type='button' class='btn btn-danger' data-bs-dismiss='modal'>Cancelar</button>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class='modal fade' id='excluir-$publicacao->id' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                                        <div class='modal-dialog modal-dialog-centered'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h5 class='modal-title' id='exampleModalLabel'>Excluir comentário</h5>
                                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                </div>
                                            <form method='POST' action='comentar'>
                                                <div class='modal-body'>
                                                    <div class='mb-3'>Deseja <b>excluir</b> o comentario \"$publicacao->comentario\" ?</div>                                            
                                                </div>
                                                <p class='text-muted mx-3 fs-6 fst-italic fw-light'>Postado às $publicacao->datahora</p>
                                            <div class='modal-footer'>
                                                <input name='donopub' value='{$donopub}' style='display: none;'> 
                                                <input name='idpublicacao' value='{$publicacao->idpublicacao}' style='display: none;'>
                                                <button type='submit' value='{$publicacao->id}' name='excluir' class='btn btn-danger'>Excluir</button>
                                                <button type='button' class='btn btn-primary' data-bs-dismiss='modal'>Cancelar</button>
                                            </div>
                                            </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 mb-3 rounded-3 shadow'> 
                                        <div class='card'>
                                            <div class='card-header'>   
                                                <img src='img/avatares/{$publicacao->avatar}.png' alt='mdo' width='32' height='32' class='rounded-circle mx-2'>
                                                <strong class='text-secondary'>{$publicacao->nome} {$publicacao->sobrenome}</strong>
                                            </div>
                                            <div class='card-body'>
                                                <blockquote class='blockquote mb-0'>
                                                <p>{$publicacao->comentario}</p>
                                                    <div class='d-grid gap-2 d-md-flex'>
                                                        <div class='text-secondary fs-6 col-8 align-self-end'><i><small class='text-muted'>{$publicacao->datahora}</small></i></div>";
        if(($dados['id'] == $publicacao->idusuario or $dados['id'] == $donopub or $dados['id']=='1') and $dados['id'] != 'anonimo'){
                $display='';
        if($dados['id'] != $publicacao->idusuario){
            $display = "none";
        }                
        $listarcomentarios .= "<div class='col-4 '>
                                    <form method='POST' action='comentar'>
                                        <input name='idpublicacao' value='{$publicacao->idpublicacao}' style='display: none;'>
                                            <div class='d-grid gap-2 d-flex justify-content-end'>                                                                                                                                               
                                                <button class='btn btn-warning btn-sm' type='button' style='display: {$display};' data-bs-toggle='modal' data-bs-target='#comentario-$publicacao->id'>Editar</button> 
                                                <button class='btn btn-danger btn-sm' type='button' data-bs-toggle='modal' data-bs-target='#excluir-$publicacao->id'>Excluir</button>
                                            </div>
                                    </form>
                                </div>
                            </div>
                        </blockquote>
                    </div>
                </div>
            </div>";
            } else {
                $listarcomentarios .= "</div>
                                        </blockquote>
                                            </div>
                                        </div>
                                    </div>";

            }
        }}
        if($listarcomentarios == ''){
           $listarcomentarios = "<div class='card text-center'>                                    
                                    <div class='card-body'>
                                        <h5 class='card-title'>Sem comentários</h5>
                                        <p class='card-text'>Seja o primeiro a comentar!</p>                                        
                                    </div>                                    
                                </div>";
        }
    return $listarcomentarios;
}  


/**
 * Exclui o comentário selecionado
 *
 * @param string $id
 * @return void
 */
function excluircomentario($id){            
    $connection = connection(); 
    $sql = "delete from comentarios where id = :id";
    $result = $connection->prepare($sql);
    $result->bindValue(':id',$id);
    $result->execute();
}


/**
 * Verifica se existe a publicação selecionada no bando de dados
 * Caso existir exibe a publicação
 * Caso não existir exibe mensagem de publicação inválida
 *
 * @return string
 */
function verificarpost() {
    global $id;
    if(mensagem($id)==''){
    $retorno = "</div>
                    <div class='card text-center col-8 mt-5'>                                    
                        <div class='card-body'>
                        <h5 class='card-title'>Publicação inválida ou inexistente</h5>
                        <p class='card-text'>Volte para o feed de notícias e clique em uma válida</p>
                        <a type='button' class='btn btn-primary' href='feed'>Voltar para o Feed de notícias...</a>                                        
                    </div>                                    
                </div>"; 
  }else{
  $retorno = mensagem($id);
  $retorno .=   "</div>
                <div class='col-5 justify-content-center border'>
                    <form method='POST' action='comentar' class='p-3'>
                        <div class='row '>
                            <div class='form-floating mb-4'>
                                 <textarea class='form-control ' placeholder='Escreva sua mensagem...' name='comentario'
                                id='floatingTextarea' style='height: 100px' required></textarea>
                            <label class='px-4' for='floatingTextarea'>Deixe sua mensagem...</label>
                            </div>
                        <div class='d-flex flex-row mb-2 justify-content-center'>
                            <div class=''>
                                <button type='submit' name='comentar' value='{$id}' class='btn btn-primary shadow-sm'>Comentar</button>
                            </div>
                    </form>
                    <form method='POST' action='comentar'>
                        <div class='mx-3'>
                            <button type='submit' name='cancelar' value='{$id}' class='btn btn-danger shadow-sm'>Cancelar</button>
                        </div>
                    </form>
                </div>
                    <h3 class='h5'>Comentários:</h3>
                <div class='box overflow-auto p-3 justify-content-center' style='height: 50vh;'>";
    $retorno .= lercomentarios($id);
    $retorno .= "</div>";
}
return $retorno;
}


/**
 * Coleta os dados das tags que serão substituídas e retorna a pagina renderizada
 *
 * @return string
 */
function page_comentar () {    
    $tags = [
        'header'    => render('pages-html/header.html', $tag = [ "nav" => verificarlogin()]),
        'conteudo'      => verificarpost(),
        'footer'    => render('pages-html/footer.html')
    ];

    return render('pages-html/comentar.html', $tags);
}

echo page_comentar();