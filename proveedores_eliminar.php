<?php
session_start();
include("auth.php");
include("conexion.php");

// ================= VALIDAR ID =================
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "ID del Proveedor no proporcionado."
    ];
    header("Location: proveedores.php");
    exit();
}

$id_proveedor = (int)$_GET['id'];

// ================= OBTENER DATOS (INCLUYE IMAGEN) =================
$stmt_check = $conn->prepare("SELECT nombre, imagen FROM proveedor WHERE id_proveedor = ?");
$stmt_check->bind_param("i", $id_proveedor);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows === 0) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Proveedor no encontrado."
    ];
    $stmt_check->close();
    header("Location: proveedores.php");
    exit();
}

$proveedor = $result->fetch_assoc();
$stmt_check->close();

// ================= ELIMINAR IMAGEN =================
if (!empty($proveedor['imagen'])) {
    $ruta_imagen = "ima_proveedores/" . $proveedor['imagen'];

    if (file_exists($ruta_imagen)) {
        unlink($ruta_imagen); // 🔥 elimina el archivo físico
    }
}

// ================= ELIMINAR REGISTRO =================
$stmt = $conn->prepare("DELETE FROM proveedor WHERE id_proveedor = ?");
$stmt->bind_param("i", $id_proveedor);

if ($stmt->execute()) {
    $_SESSION['mensaje'] = [
        "tipo" => "success",
        "texto" => "Proveedor eliminado correctamente. 🛢️"
    ];
} else {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Error al eliminar Proveedor: " . $stmt->error
    ];
}

$stmt->close();
$conn->close();

// ================= REDIRECCIÓN =================
header("Location: proveedores.php");
exit();
?>