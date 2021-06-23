<?php

include 'template.php';

$_SESSION['acesso'] = false;
unset($_SESSION['usuario']);

header ('location: '. URL . '/bemvindo');

