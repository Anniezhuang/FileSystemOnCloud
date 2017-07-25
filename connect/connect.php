<?php
// PDO + MySQL
$hostname="";
$dbname="";
$password="";
$username="";

$pdo = new PDO('mysql:host=$hostname;dbname=$dbname, $username, $password');
