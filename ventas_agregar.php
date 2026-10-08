<?php
session_start();

include("auth.php");
include("conexion.php");

// ================= CREAR VENTA RÁPIDA =================

$id_usuario = (int) $_SESSION['id_usuario'];
$codigo = "COD-" . strtoupper(substr(uniqid(), -5));

$stmt = $conn->prepare("
    INSERT INTO venta (codigo, id_usuario, total) 
    VALUES (?, ?, 0)
");

$stmt->bind_param("si", $codigo, $id_usuario);

if ($stmt->execute()) {

    // OBTENER ID DE LA VENTA CREADA
    $id_venta = $conn->insert_id;

    $_SESSION['mensaje'] = [
        "tipo" => "success",
        "texto" => "Venta creada correctamente."
    ];

    // REDIRECCIÓN A DETALLE
    header("Location: ventas_detalles.php?id=" . $id_venta);
    exit();

} else {

    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Error al crear venta: " . $stmt->error
    ];

    header("Location: ventas.php");
    exit();
}

$stmt->close();
$conn->close();
?>