<?php
include("auth.php"); // 👈 protege la página
include("conexion.php");
// ================= CONEXIÓN =================


// ================= VALIDAR ID =================
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "ID no especificado ❌"
    ];
    header("Location: empleados.php");
    exit();
}

$id = intval($_GET['id']); // 🔥 sanitizar

// ================= OBTENER IMAGEN =================
$sql = "SELECT imagen FROM empleado WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "Error en la consulta ❌ " . $conn->error
    ];
    header("Location: empleados.php");
    exit();
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$empleado = $result->fetch_assoc();

if (!$empleado) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "Empleado no encontrado ❌"
    ];
    header("Location: empleados.php");
    exit();
}

// ================= ELIMINAR IMAGEN =================
$archivo = basename($empleado['imagen']);
$ruta = "ima_empleados/" . $archivo;

if ($archivo && file_exists($ruta)) {
    unlink($ruta);
}

// ================= ELIMINAR EMPLEADO =================
$sql_delete = "DELETE FROM empleado WHERE id_empleado = ?";
$stmt_delete = $conn->prepare($sql_delete);

// Validar prepare
if (!$stmt_delete) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "Error en la consulta ❌ " . $conn->error
    ];
    header("Location: empleados.php");
    exit();
}

$stmt_delete->bind_param("i", $id);

if ($stmt_delete->execute()) {

    $_SESSION['mensaje'] = [
        'tipo' => 'success',
        'texto' => "Empleado eliminado correctamente 🗑️"
    ];

} else {

    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "Error al eliminar ❌ " . $stmt_delete->error
    ];
}

// ================= REDIRECCIÓN =================
header("Location: empleados.php");
exit();

// ================= CIERRE =================
$stmt_delete->close();
$conn->close();
?>