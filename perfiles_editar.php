<?php
include("auth.php"); // protege la página
include("conexion.php");

// MENSAJE DESDE SESIÓN
$mensaje = null;
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

// ================= ID DEL PERFIL =================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "ID de perfil inválido ❌"
    ];
    header("Location: perfiles.php");
    exit();
}

$id_perfil = intval($_GET['id']);

// ================= OBTENER DATOS EXISTENTES =================
$sql = "SELECT nombre, descripcion FROM perfil WHERE id_perfil = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_perfil);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "Perfil no encontrado ❌"
    ];
    header("Location: perfiles.php");
    exit();
}

$perfil = $result->fetch_assoc();
$stmt->close();

// ================= PROCESAR FORMULARIO =================
if (isset($_POST['guardar'])) {

    // 🔐 Sanitizar datos
    $nombre = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
    $descripcion = htmlspecialchars(trim($_POST['descripcion']), ENT_QUOTES, 'UTF-8');

    // ❌ Validar nombre
    if (empty($nombre)) {
        $_SESSION['mensaje'] = [
            'tipo' => 'error',
            'texto' => "El nombre es obligatorio ❌"
        ];
        header("Location: perfiles_editar.php?id=$id_perfil");
        exit();
    }

    // 🗄️ ACTUALIZAR EN BD
    $sql = "UPDATE perfil SET nombre = ?, descripcion = ? WHERE id_perfil = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $descripcion, $id_perfil);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = [
            'tipo' => 'success',
            'texto' => "Perfil actualizado correctamente ☑️\nNombre: $nombre"
        ];
    } else {
        $_SESSION['mensaje'] = [
            'tipo' => 'error',
            'texto' => "Error al actualizar perfil ❌"
        ];
    }

    $stmt->close();

    // 🔄 Redirección segura
    header("Location: perfiles.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil CAJA - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="css/style_perfiles_editar.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script defer src="js/script_perfiles_editar.js"></script>
</head>
<body>

<!-- Toast -->
<div id="toast" class="toast"></div>
<script>
window.addEventListener('DOMContentLoaded', () => {
    const toastDiv = document.getElementById('toast');
    const mensaje = <?php echo $mensaje ? json_encode($mensaje) : 'null'; ?>;
    if (mensaje) {
        toastDiv.classList.add(mensaje.tipo);
        toastDiv.innerHTML = mensaje.texto;
        toastDiv.style.opacity = 1;
        toastDiv.style.transform = 'translateY(0)';
        setTimeout(() => {
            toastDiv.style.opacity = 0;
            toastDiv.style.transform = 'translateY(-20px)';
        }, 4000);
    }
});
</script>

<div class="sidebar" id="sidebar">
        
        <button id="toggle-btn">
            <i class="fas fa-bars"></i>
        </button>

        <ul>
            <br>
            <li class="submenu" style="list-style: none;">
                <a href="#" class="submenu-toggle">
                    <img src="img/1.png" alt="Perfil" class="avatar">
                    <span><?php echo $usuarioSesion; ?></span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list" >
                    <li><a href="mi_perfil.php"><i class="fas fa-user"></i><span>Mi Perfil</span></a></li>
                    <li><a href="configuracion.html"><i class="fas fa-cog"></i><span>Configuración</span></a></li>
                    <li><a href="logout.php"><i class="fas fa-right-from-bracket"></i><span>Cerrar Sesión</span></a></li>
                </ul>
            </li>
            <!--<button id="toggle-btn"><i class="fas fa-bars"></i></button><br>-->

            <li><a href="dashboard.php"><i class="fas fa-th-large"></i><span>Dashboard</span></a></li>
            <li><a href="ventas.php"><i class="fas fa-shopping-cart"></i><span>Ventas</span></a></li>
            <li><a href="compras.php"><i class="fas fa-box"></i><span>Compras</span></a></li>
            <li><a href="inventario.php"><i class="fas fa-clipboard-list"></i><span>Inventario</span></a></li>
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-boxes"></i>
                    <span>Productos</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="p_listaproductos.html"><i class="fas fa-user-tie"></i><span>Lista Productos</span></a></li>
                    <li><a href="p_agregarproductos.html"><i class="fas fa-id-badge"></i><span>Agregar Productos</span></a></li>
                    <li><a href="p_categorias.html"><i class="fas fa-user-cog"></i><span>Categorías</span></a></li>
                </ul>
            </li>
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-users"></i>
                    <span>Personas</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="empleados.php"><i class="fas fa-user-tie"></i><span>Empleados</span></a></li>
                    <li><a href="perfiles.php"><i class="fas fa-id-badge"></i><span>Perfiles</span></a></li>
                    <li><a href="usuarios.php"><i class="fas fa-user-cog"></i><span>Usuarios</span></a></li>
                    <li><a href="proveedores.php"><i class="fas fa-truck"></i><span>Proveedores</span></a></li>
                    <li><a href="clientes.php"><i class="fas fa-handshake"></i><span>Clientes</span></a></li>
                </ul>
            </li>
            <li><a href="reportes.php"><i class="fas fa-folder-open"></i><span>Reportes</span></a></li>
            <li><a href="caja.php"><i class="fas fa-envelope"></i><span>Caja</span></a></li>
        </ul>       
    </div>

<main class="contenido">
    <header class="dashboard-header">
        <div class="header-top">
            <h1>Editar Perfil - Caja POS</h1>
            <a href="dashboard.php" class="header-logo">
                <img src="img/s_fondo.png" alt="Omega Logo">
            </a>
        </div>
        <div class="header-bottom">
            <div>Usuario: <strong><?php echo $usuarioSesion; ?></strong>👋</div>
            <div>Caja: <strong>Activa</strong></div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-right-from-bracket"></i> Cerrar sesión   
            </a>
        </div>
    </header>

    <div class="chart-container">
        <div class="products-header">
            <h3>Editar Datos del Perfil</h3>
            <a href="perfiles.php" class="new-btn">
                <i class="fas fa-reply"></i> Regresar  
            </a>
        </div>

        <form action="" method="POST" class="form-producto">
            <div class="form-group">
                <label>Nombre del perfil:</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($perfil['nombre']) ?>" required>
            </div>

            <div class="form-group full">
                <label>Descripción:</label>
                <textarea name="descripcion" rows="4"><?= htmlspecialchars($perfil['descripcion']) ?></textarea>
            </div>

            <div class="form-buttons">
                <button type="submit" name="guardar">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <a href="perfiles.php">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <footer class="footer">
        <p class="copy">© 2026 Farmacias Omega Oaxaca - Todos los derechos reservados</p>
    </footer>
</main>

</body>
</html>

<?php $conn->close(); ?>