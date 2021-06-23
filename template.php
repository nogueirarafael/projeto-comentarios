<?php
include "function.php";

/**
 * Responsável pela substituição das tags e retornar a página HTML
 *
 * @param string $page
 * @param array $tags
 * @return string
 */
function render ($page, $tags=[]) {

    $addSymbol = fn($tag) => '{{' . $tag . '}}';
    $keys = array_map($addSymbol, array_keys($tags));

    $template = file_get_contents(__dir__ . "/{$page}");

    return  str_replace($keys, $tags, $template);
}

