<?php
$host = "localhost";
$usuario = "root";
$password = "";
$bd = "omega2";

$conn = new mysqli($host, $usuario, $password, $bd);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>