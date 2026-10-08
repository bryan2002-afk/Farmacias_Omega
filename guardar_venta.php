<?php

include("conexion.php");

header('Content-Type: application/json');

// RECIBIR JSON
$data = json_decode(file_get_contents("php://input"), true);

if(!$data){

    echo json_encode([
        'success' => false,
        'mensaje' => 'Datos inválidos'
    ]);

    exit;
}

// DATOS
$idVenta   = intval($data['id_venta']);
$subtotal  = floatval($data['subtotal']);
$iva       = floatval($data['iva']);
$total     = floatval($data['total']);
$productos = $data['productos'];


// INICIAR TRANSACCIÓN
$conn->begin_transaction();

try{

    // =========================================
    // ACTUALIZAR TABLA VENTA
    // =========================================

    $sqlVenta = "UPDATE venta 
                 SET subtotal = ?, 
                     iva = ?, 
                     total = ?
                 WHERE id_venta = ?";

    $stmtVenta = $conn->prepare($sqlVenta);

    $stmtVenta->bind_param(
        "dddi",
        $subtotal,
        $iva,
        $total,
        $idVenta
    );

    $stmtVenta->execute();


    // =========================================
    // INSERTAR DETALLE VENTA
    // =========================================

    $sqlDetalle = "INSERT INTO detalle_venta
    (
        id_venta,
        id_articulo,
        cantidad,
        precio,
        subtotal
    )
    VALUES (?, ?, ?, ?, ?)";

    $stmtDetalle = $conn->prepare($sqlDetalle);


    // =========================================
    // ACTUALIZAR STOCK
    // =========================================

    $sqlStock = "UPDATE articulo
                 SET stock = stock - ?
                 WHERE id_articulo = ?";

    $stmtStock = $conn->prepare($sqlStock);


    foreach($productos as $producto){

        $idArticulo = intval($producto['id_articulo']);
        $cantidad   = intval($producto['cantidad']);
        $precio     = floatval($producto['precio']);
        $sub        = floatval($producto['subtotal']);

        // INSERT DETALLE
        $stmtDetalle->bind_param(
            "iiidd",
            $idVenta,
            $idArticulo,
            $cantidad,
            $precio,
            $sub
        );

        $stmtDetalle->execute();

        // UPDATE STOCK
        $stmtStock->bind_param(
            "ii",
            $cantidad,
            $idArticulo
        );

        $stmtStock->execute();
    }

    // CONFIRMAR
    $conn->commit();

    echo json_encode([
        'success' => true,
        'mensaje' => 'Venta guardada correctamente'
    ]);

}catch(Exception $e){

    $conn->rollback();

    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ]);
}
?>