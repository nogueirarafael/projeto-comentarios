<?php

const PATH_ROOT = __DIR__;
const URL = 'http://localhost/FATEC/P3';

$host = 'meubanco.ctgohzbq3djs.us-east-1.rds.amazonaws.com';
$port = '3306';
$db = 'P2';
$user = 'admin';
$passwd = 'senhabanco';

// Create Database P2;

// Use P2;

// Create Table usuarios
// (
//     id int primary key auto_increment,
//     nome VARCHAR(30),
//     sobrenome VARCHAR(30),
//     email VARCHAR(30),
//     senha VARCHAR(60),
//     cidade VARCHAR(30),
//     estado VARCHAR(30),
//     avatar VARCHAR(40)
// );

// insert into usuarios (nome,sobrenome,email,senha,avatar,cidade,estado)
//                 values('admin','','admin@email.com','$2y$12$JKpFCoYNKaUOCCZuGVAtMeY9/eL7sUbmgqwiq85tVDvUqzadNL.oK','admin','','');

// Create Table publicacao
// (
//     id int primary key auto_increment,
//     idusuario VARCHAR(3),
//     titulo VARCHAR(50),
//     mensagem VARCHAR(10000),    
//     nome VARCHAR(60),
//     sobrenome VARCHAR(30),
//     avatar VARCHAR(40),
//     imagempost VARCHAR(40),
//     datahora VARCHAR(50) 
// );

// Create Table comentarios
// (
//     id int primary key auto_increment,
//     idpublicacao VARCHAR(3),
//     idusuario VARCHAR(7),
//     comentario VARCHAR(10000),    
//     nome VARCHAR(60),
//     sobrenome VARCHAR(30),
//     avatar VARCHAR(40),
//     datahora VARCHAR(50)    
// );