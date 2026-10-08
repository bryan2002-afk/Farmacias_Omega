<?php
session_start();
include("auth.php");      // Verifica sesión
include("conexion.php");  // Conexión a la base de datos

// ================= VALIDAR ID =================
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "ID de la Dirección no proporcionado."
    ];
    header("Location: direcciones.php");
    exit();
}

$id_direccion = (int)$_GET['id'];




// ================= VERIFICAR QUE EL USUARIO EXISTE =================
$stmt_check = $conn->prepare("SELECT codigo_postal FROM direccion WHERE id_direccion = ?");
$stmt_check->bind_param("i", $id_direccion);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows === 0) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Dirección no encontrada."
    ];
    $stmt_check->close();
    header("Location: direcciones.php");
    exit();
}

$articuloo = $result->fetch_assoc();
$stmt_check->close();

// ================= ELIMINAR USUARIO =================
$stmt = $conn->prepare("DELETE FROM direccion WHERE id_direccion = ?");
$stmt->bind_param("i", $id_direccion);

if ($stmt->execute()) {
    

    $_SESSION['mensaje'] = [
        "tipo" => "success",
        "texto" => "Dirección eliminada correctamente. 🛢️"
    ];
} else {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Error al eliminar Dirección: " . $stmt->error
    ];
}

$stmt->close();
$conn->close();

// ================= REDIRECCIÓN =================
header("Location: direcciones.php");
exit();
?>