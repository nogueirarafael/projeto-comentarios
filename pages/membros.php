<?php

include 'template.php';

if ($dados['id'] == '1'){
        $action = "<th style='width:10%;' scope='col'>Ação</th>";
}

                                       
if(isset($_POST['excluir'])){ 
    $id = $_POST["excluir"] ?? '';
    $deletar = $_POST["deletar"] ?? '';
    if($deletar == 'deletar' or $deletar == 'DELETAR'){
        excluirusuario($id);
        $erro = "<div class='alert alert-success text-center mx-auto' style='width: 600px;'>Usuário excluido!</div>";
    
    } else {
        $erro = "<div class='alert alert-danger text-center mx-auto' style='width: 600px;'>Ocorreu um erro ao tentar excluir, tente novamente!</div>";
    }
}

/**
 * Busca todos os membros cadatrados no site no banco de dados na tabela usuarios e retorna html
 * Caso usuário conectado for 'admin' exibe botão para excluir os usuários, caso não o botão excluir fica oculto
 *
 * @return string
 */
function lermembros(){
    global $dados;
    $membros = '';
    $connection = connection();
    $sql = "select * from usuarios";
    $result = $connection->prepare($sql);
    $result->execute();
    $clientes = $result->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($clientes as $value) {
        if($value['id'] != '1') {
            $membros .= "<tr>                    
                        <td class=\"align-middle\"><img src=\"img/avatares/{$value["avatar"]}.png\" width='50px' height='50px' class='rounded-circle'></td>                   
                        <td class=\"align-middle\">{$value["nome"]}</td>                   
                        <td class=\"align-middle\">{$value["sobrenome"]}</td>                   
                        <td class=\"align-middle\">{$value["email"]}</td>                    
                        <td class=\"align-middle\">{$value["cidade"]}</td>
                        <td class=\"align-middle\">{$value["estado"]}</td>";
        if ($dados['id'] == '1'){
            $membros .= "<td>                            
                            <div class='d-flex flex-row'>                                    
                                 <div class='col mx-2'>
                                    <button type='button' name='delet' class='btn btn-danger' type='button' data-bs-toggle='modal' data-bs-target='#excluir-{$value['id']}'>Excluir</button>
                                 </div>
                             </div>
                        </td> 
                    </tr>   
                <div class='modal fade' id='excluir-{$value['id']}' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                        <div class='modal-dialog modal-dialog-centered'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h5 class='modal-title' id='exampleModalLabel'>Deseja excluir o usuário {$value['nome']} {$value['sobrenome']}?</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <form method='POST' action='membros'>
                                    <div class='modal-body'>
                                        <div class='mb-3'>
                                            
                                            <div class='col row mb-3'>
                                                <div class='col-12 mb-3'>
                                                <label for='senhaatual' class='form-label'>Escreva DELETAR para excluir</label>
                                                <input type='text' class='form-control' id='deletar' name='deletar'
                                                    placeholder='...' required>
                                                </div>  
                                            </div>

                                        </div>                                            
                                    </div>
                                <div class='modal-footer'>                    
                                        <button type='submit' value='{$value['id']}' name='excluir' class='btn btn-danger'>Excluir</button>
                                        <button type='button' class='btn btn-primary' data-bs-dismiss='modal'>Cancelar</button>
                                </div>
                                </form>
                            </div>
                        </div>
                        </div>";
        }  else {                  
        $membros .= "</tr>"; 
        }
    }    
}
return $membros;
}

/**
 * Coleta os dados das tags que serão substituídas e retorna a pagina renderizada
 *
 * @return string
 */
function page_membros () {
    global $action, $erro;    
    $tags = [
        'header'    => render('pages-html/header.html', $tag = [ "nav" => verificarlogin()]),
        'erro'      => $erro,
        'action'    => $action ?? '',
        'listarmembros'   => lermembros(),
        'footer'    => render('pages-html/footer.html')
    ];

    return render('pages-html/membros.html', $tags);
}

echo page_membros();
