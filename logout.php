<?php
session_start();
include("conexion.php");

if (isset($_SESSION['id_usuario'])) {

    $id_usuario = $_SESSION['id_usuario'];

    // ================= OBTENER APERTURA ACTIVA =================
    $sql = "SELECT * FROM apertura_caja 
            WHERE id_usuario = ? AND estado = 1 
            ORDER BY id_apertura DESC LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $apertura = $result->fetch_assoc();
        $id_apertura = $apertura['id_apertura'];

        // ================= CERRAR APERTURA =================
        $update = "UPDATE apertura_caja 
                   SET estado = 0 
                   WHERE id_apertura = ?";

        $stmtUpdate = $conn->prepare($update);
        $stmtUpdate->bind_param("i", $id_apertura);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        // ================= INSERTAR CIERRE DE CAJA =================
        $monto_final = 0.00; // 🔥 aquí luego conectamos ventas reales

        $insert = "INSERT INTO cierre_caja (id_apertura, monto_final)
                   VALUES (?, ?)";

        $stmtInsert = $conn->prepare($insert);
        $stmtInsert->bind_param("id", $id_apertura, $monto_final);
        $stmtInsert->execute();
        $stmtInsert->close();
    }

    $stmt->close();
}

// ================= DESTRUIR SESIÓN =================
session_destroy();
header("Location: 1_login.php");
exit();
?>