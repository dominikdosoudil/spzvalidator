<?php
/**
 * Created by PhpStorm.
 * User: ddos
 * Date: 01.01.17
 * Time: 20:28
 */


if(!isset($_GET['spz']) && !isset($_POST['spz'])){
    echo $_POST['spz'];
    http_response_code(400);
    return;
}

$db = new PDO('mysql:host=localhost;dbname=spz;port=5432', 'root', 'root');

if(isset($_GET['spz'])) {
    $sth = $db->prepare('SELECT * FROM spzlist WHERE spz = :spz LIMIT 1');
    $sth->execute([':spz' => $_GET['spz']]);
    echo json_encode($sth->rowCount());
} else {
    $sth = $db->prepare('INSERT INTO spzlist (spz) VALUES (:spz)');
    $sth->execute([':spz' => $_POST['spz']]);
    echo "1";
}