<?php
session_start();
include("auth.php");      // Verifica sesión
include("conexion.php");  // Conexión a la base de datos

// ================= VALIDAR ID =================
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "ID de usuario no proporcionado."
    ];
    header("Location: usuarios.php");
    exit();
}

$id_usuario = (int)$_GET['id'];

// ================= VERIFICAR QUE EL USUARIO EXISTE =================
$stmt_check = $conn->prepare("SELECT imagen FROM usuarios WHERE id_usuario = ?");
$stmt_check->bind_param("i", $id_usuario);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows === 0) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Usuario no encontrado."
    ];
    $stmt_check->close();
    header("Location: usuarios.php");
    exit();
}

$usuario = $result->fetch_assoc();
$stmt_check->close();

// ================= ELIMINAR USUARIO =================
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);

if ($stmt->execute()) {
    // ================= ELIMINAR IMAGEN DEL USUARIO =================
    if (!empty($usuario['imagen'])) {
        $ruta_imagen = 'ima_usuarios/' . $usuario['imagen'];
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen); // elimina archivo
        }
    }

    $_SESSION['mensaje'] = [
        "tipo" => "success",
        "texto" => "Usuario eliminado correctamente."
    ];
} else {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Error al eliminar usuario: " . $stmt->error
    ];
}

$stmt->close();
$conn->close();

// ================= REDIRECCIÓN =================
header("Location: usuarios.php");
exit();
?>