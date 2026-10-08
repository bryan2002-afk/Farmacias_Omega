<?php
session_start();
include("auth.php");      // Verifica sesión
include("conexion.php");  // Conexión a la base de datos

// ================= VALIDAR ID =================
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "ID del Cliente no proporcionado."
    ];
    header("Location: clientes.php");
    exit();
}

$id_cliente = (int)$_GET['id'];




// ================= VERIFICAR QUE EL USUARIO EXISTE =================
$stmt_check = $conn->prepare("SELECT nombre FROM cliente WHERE id_cliente = ?");
$stmt_check->bind_param("i", $id_cliente);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows === 0) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Cliente no encontrado."
    ];
    $stmt_check->close();
    header("Location: clientes.php");
    exit();
}

$clientee = $result->fetch_assoc();
$stmt_check->close();

// ================= ELIMINAR USUARIO =================
$stmt = $conn->prepare("DELETE FROM cliente WHERE id_cliente = ?");
$stmt->bind_param("i", $id_cliente);

if ($stmt->execute()) {

    $_SESSION['mensaje'] = [
        "tipo" => "success",
        "texto" => "Cliente eliminado correctamente. 🛢️"
    ];
} else {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Error al eliminar Cliente: " . $stmt->error
    ];
}

$stmt->close();
$conn->close();

// ================= REDIRECCIÓN =================
header("Location: clientes.php");
exit();
?>