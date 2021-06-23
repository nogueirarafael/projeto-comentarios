<?php

include 'template.php';


/**
 * Coleta os dados das tags que serão substituídas e retorna a pagina renderizada
 *
 * @return string
 */
function page_feed () {    
    $tags = [
        'header'    => render('pages-html/header.html', $tag = [ "nav" => verificarlogin()]),
        'feed'      => verificarpublicacao(),
        'footer'    => render('pages-html/footer.html')
    ];

    return render('pages-html/feed.html', $tags);
}

echo page_feed();
