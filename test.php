<?php
include 'DatabaseClass.php';
$Articles = Database::UserDB();
print_r($Articles->getAll()[0]);
?>