<?php

include 'template.php';


/**
 * Coleta os dados das tags que serão substituídas e retorna a pagina renderizada
 *
 * @return string
 */
function page_bemvindo () {

    $tags = [
        'header'    => render('pages-html/header.html', $tag = [ "nav" => verificarlogin()]),
        'footer'    => render('pages-html/footer.html')
    ];

    return render('pages-html/bemvindo.html', $tags);
}

echo page_bemvindo();
