<?php
// ================= SESIÓN =================
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Evitar acceso sin login
// ================= VALIDAR SESIÓN =================
if (!isset($_SESSION['usuario'])) {
    header("Location: 1_login.php");
    exit();
}

// Evitar cache (opcional pero pro)
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// ================= DATOS DEL USUARIO =================
$usuarioSesion = $_SESSION['usuario']; // Nombre de usuario
$id_empleado = $_SESSION['id_empleado']; // ID del empleado

// IMAGEN DEL USUARIO
$imagenUsuario = $_SESSION['imagen_usuario'];

// Conectar a la base de datos
include('conexion.php');

// Obtener el nombre del empleado desde la base de datos
$sqlEmpleado = "SELECT nombre FROM empleado WHERE id_empleado = ?";
$stmtEmpleado = $conn->prepare($sqlEmpleado);
$stmtEmpleado->bind_param("i", $id_empleado);
$stmtEmpleado->execute();
$resultEmpleado = $stmtEmpleado->get_result();

if ($resultEmpleado && $filaEmpleado = $resultEmpleado->fetch_assoc()) {
    $nombreEmpleado = $filaEmpleado['nombre']; // Almacenar nombre del empleado en la variable
} else {
    $nombreEmpleado = "Desconocido"; // Si no se encuentra el nombre, asignamos "Desconocido"
}

$stmtEmpleado->close();
?>