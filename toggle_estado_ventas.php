<?php
include("conexion.php");

if (isset($_GET['id'])) {

    $id = intval($_GET['id']);

    // 1. Obtener estado actual desde la BD (NO desde GET)
    $stmt = $conn->prepare("SELECT estado FROM venta WHERE id_venta = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        $estado_actual = (int)$row['estado'];

        // 2. Calcular nuevo estado (ciclo)
        if ($estado_actual == 0) {
            $nuevo_estado = 1;
        } elseif ($estado_actual == 1) {
            $nuevo_estado = 2;
        } else {
            $nuevo_estado = 0;
        }

        // 3. Actualizar con prepared statement
        $stmt = $conn->prepare("UPDATE venta SET estado = ? WHERE id_venta = ?");
        $stmt->bind_param("ii", $nuevo_estado, $id);

        if ($stmt->execute()) {
            header("Location: ventas.php");
            exit;
        } else {
            echo "Error al actualizar estado";
        }

    } else {
        echo "Venta no encontrada";
    }
}

$conn->close();
?>