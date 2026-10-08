<?php
include("auth.php"); // protege la página
include("conexion.php"); // conexión a la BD

// ================================
// ELIMINAR CATEGORÍA
// ================================

// Comprobar si se pasó el ID por GET
if (isset($_GET['id'])) {
    $id_categoria = intval($_GET['id']); // convertir a entero para seguridad

    // Verificar si la categoría existe
    $check = $conn->prepare("SELECT nombre FROM categoria WHERE id_categoria = ?");
    $check->bind_param("i", $id_categoria);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $check->bind_result($nombre);
        $check->fetch();

        // Eliminar la categoría
        $stmt = $conn->prepare("DELETE FROM categoria WHERE id_categoria = ?");
        $stmt->bind_param("i", $id_categoria);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = [
                'tipo' => 'success',
                'texto' => "Categoría '$nombre' eliminada correctamente ☑️"
            ];
        } else {
            $_SESSION['mensaje'] = [
                'tipo' => 'error',
                'texto' => "Error al eliminar la categoría ❌"
            ];
        }

        $stmt->close();
    } else {
        $_SESSION['mensaje'] = [
            'tipo' => 'error',
            'texto' => "No se encontró la categoría ❌"
        ];
    }

    $check->close();
} else {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "ID de categoría no válido ❌"
    ];
}

// Redirigir a la lista de categorías
header("Location: categorias.php");
exit();

$conn->close();
?>