<?php
include("auth.php"); // protege la página
include("conexion.php");

// ================= VALIDAR ID =================
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "ID no especificado ❌"
    ];
    header("Location: perfiles.php");
    exit();
}

$id = intval($_GET['id']);

// ================= VERIFICAR EXISTENCIA =================
$sql = "SELECT nombre FROM perfil WHERE id_perfil = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "Error en la consulta ❌"
    ];
    header("Location: perfiles.php");
    exit();
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$perfil = $result->fetch_assoc();

if (!$perfil) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "Perfil no encontrado ❌"
    ];
    header("Location: perfiles.php");
    exit();
}

$stmt->close();

// ================= ELIMINAR =================
$sql_delete = "DELETE FROM perfil WHERE id_perfil = ?";
$stmt_delete = $conn->prepare($sql_delete);

if (!$stmt_delete) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "Error en la consulta ❌"
    ];
    header("Location: perfiles.php");
    exit();
}

$stmt_delete->bind_param("i", $id);

if ($stmt_delete->execute()) {

    $_SESSION['mensaje'] = [
        'tipo' => 'success',
        'texto' => "Perfil eliminado correctamente 🗑️\nNombre: " . $perfil['nombre']
    ];

} else {

    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "Error al eliminar ❌"
    ];
}

$stmt_delete->close();
$conn->close();

// ================= REDIRECCIÓN =================
header("Location: perfiles.php");
exit();
?>