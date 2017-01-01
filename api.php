<?php
/**
 * Created by PhpStorm.
 * User: ddos
 * Date: 01.01.17
 * Time: 20:28
 */


if(!isset($_GET['spz'])){
    http_response_code(400);
    return;
}

$db = new PDO('mysql:host=localhost;dbname=spz;port=5432', 'root', 'root');
$sth = $db->prepare('SELECT * FROM spzlist WHERE spz = :spz LIMIT 1');
$sth->execute([':spz' => $_GET['spz']]);
echo json_encode($sth->rowCount());