<?php
include ('config.php');

session_start();


$rota = $_GET['url'] ?? 'bemvindo';

$_SESSION['acesso'] = $_SESSION['acesso'] ?? false;
if (file_exists("pages/{$rota}.php")){
    include "pages/{$rota}.php";
} else {
    header ('location: '. URL . '/bemvindo');
}
