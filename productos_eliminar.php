<?php
session_start();
include("auth.php");      // Verifica sesión
include("conexion.php");  // Conexión a la base de datos

// ================= VALIDAR ID =================
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "ID de Articulo no proporcionado."
    ];
    header("Location: productos_lista.php");
    exit();
}

$id_articulo = (int)$_GET['id'];




// ================= VERIFICAR QUE EL USUARIO EXISTE =================
$stmt_check = $conn->prepare("SELECT imagen FROM articulo WHERE id_articulo = ?");
$stmt_check->bind_param("i", $id_articulo);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows === 0) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Articulo no encontrado."
    ];
    $stmt_check->close();
    header("Location: productos_lista.php");
    exit();
}

$articuloo = $result->fetch_assoc();
$stmt_check->close();

// ================= ELIMINAR USUARIO =================
$stmt = $conn->prepare("DELETE FROM articulo WHERE id_articulo = ?");
$stmt->bind_param("i", $id_articulo);

if ($stmt->execute()) {
    // ================= ELIMINAR IMAGEN DEL USUARIO =================
    if (!empty($articuloo['imagen'])) {
        $ruta_imagen = 'ima_productos/' . $articuloo['imagen'];
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen); // elimina archivo
        }
    }

    $_SESSION['mensaje'] = [
        "tipo" => "success",
        "texto" => "Producto eliminado correctamente. 🛢️"
    ];
} else {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Error al eliminar Articulo: " . $stmt->error
    ];
}

$stmt->close();
$conn->close();

// ================= REDIRECCIÓN =================
header("Location: productos_lista.php");
exit();
?>