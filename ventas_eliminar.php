<?php
session_start();
include("auth.php");
include("conexion.php");

// ================= VALIDAR ID =================
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "ID de la venta no proporcionado."
    ];
    header("Location: ventas.php");
    exit();
}

$id_venta = (int)$_GET['id'];

// ================= OBTENER DATOS (INCLUYE IMAGEN) =================
$stmt_check = $conn->prepare("SELECT * FROM venta WHERE id_venta = ?");
$stmt_check->bind_param("i", $id_venta);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows === 0) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Venta no encontrada."
    ];
    $stmt_check->close();
    header("Location: ventas.php");
    exit();
}

$venta = $result->fetch_assoc();
$stmt_check->close();



// ================= ELIMINAR REGISTRO =================
$stmt = $conn->prepare("DELETE FROM venta WHERE id_venta = ?");
$stmt->bind_param("i", $id_venta);

if ($stmt->execute()) {
    $_SESSION['mensaje'] = [
        "tipo" => "success",
        "texto" => "Venta eliminada correctamente. 🛢️"
    ];
} else {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Error al eliminar Venta: " . $stmt->error
    ];
}

$stmt->close();
$conn->close();

// ================= REDIRECCIÓN =================
header("Location: ventas.php");
exit();
?>