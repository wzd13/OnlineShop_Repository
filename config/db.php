<?php

$host = "localhost";
$user = "root";
$pass = "1234";
$dbname = "taohu_db";

$conn = new mysqli($host,$user,$pass,$dbname);

if ($conn->connect_error) {
    die("Connection Failed!: " . $conn->connect_error);
}
?>