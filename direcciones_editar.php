<?php
include("auth.php");
include("conexion.php");

// ================= MENSAJE =================
$mensaje = null;
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

// ================= VALIDAR ID =================
if (!isset($_GET['id'])) {
    header("Location: direcciones.php");
    exit();
}

$id = intval($_GET['id']);

// ================= OBTENER REGISTRO =================
$stmt = $conn->prepare("SELECT * FROM direccion WHERE id_direccion = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: direcciones.php");
    exit();
}

$direccion = $result->fetch_assoc();

// ================= ACTUALIZAR =================
if (isset($_POST['guardar'])) {

    $calle = trim($_POST['calle']);
    $numero = trim($_POST['numero']);
    $colonia = trim($_POST['colonia']);
    $municipio = trim($_POST['municipio']);
    $codigo_postal = trim($_POST['codigo_postal']);
    $ciudad = trim($_POST['ciudad']);
    $estado = trim($_POST['estado']);
    $pais = trim($_POST['pais']);
    $referencia = trim($_POST['referencia']);

    if (
        empty($calle) || empty($numero) || empty($colonia) ||
        empty($municipio) || empty($codigo_postal) ||
        empty($ciudad) || empty($estado) ||
        empty($pais) || empty($referencia)
    ) {
        $_SESSION['mensaje'] = [
            "tipo" => "error",
            "texto" => "Todos los campos son obligatorios."
        ];

        header("Location: direcciones_editar.php?id=$id");
        exit();
    }

    $sql = "UPDATE direccion SET
            calle = ?,
            numero = ?,
            colonia = ?,
            municipio = ?,
            codigo_postal = ?,
            ciudad = ?,
            estado = ?,
            pais = ?,
            referencia = ?
            WHERE id_direccion = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssssssi",
        $calle,
        $numero,
        $colonia,
        $municipio,
        $codigo_postal,
        $ciudad,
        $estado,
        $pais,
        $referencia,
        $id
    );

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = [
            "tipo" => "success",
            "texto" => "Dirección actualizada correctamente."
        ];
        header("Location: direcciones.php");
        exit();
    } else {
        $_SESSION['mensaje'] = [
            "tipo" => "error",
            "texto" => "Error al actualizar."
        ];
        header("Location: direcciones_editar.php?id=$id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Direcciónes CAJA - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="css/style_usuarios_agregar.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_empleados_agregar.js"></script>
</head>



<body>

    <!-- Toast -->
    <div id="toast" class="toast"></div>

    <script>
    window.addEventListener('DOMContentLoaded', () => {
        const toastDiv = document.getElementById('toast');

         // Usar la variable $mensaje que ya definimos en PHP
        const mensaje = <?php echo $mensaje ? json_encode($mensaje) : 'null'; ?>;

        if (mensaje) {
            toastDiv.classList.add(mensaje.tipo); // success o error
            //toastDiv.textContent = mensaje.texto;
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
                    <li><a href="direcciones.php"><i class="fas fa-map-marker-alt"></i><span>Direcciónes</span></a></li>

                </ul>
            </li>
            <li><a href="reportes.php"><i class="fas fa-folder-open"></i><span>Reportes</span></a></li>
            <li><a href="caja.php"><i class="fas fa-envelope"></i><span>Caja</span></a></li>
        </ul>       
    </div>

    
    <main class="contenido">
        
        <header class="dashboard-header">
    
            <!-- FILA SUPERIOR -->
            <div class="header-top">
                <div>
                    <h1>Editar Dirección - Caja POS</h1>
                    
                </div>

                <a href="dashboard.php" class="header-logo">
                    <img src="img/s_fondo.png" alt="Omega Logo">
                </a>
            </div>

            <!-- FILA INFERIOR -->
            <div class="header-bottom">
                <div>Usuario: <strong><?php echo $usuarioSesion; ?></strong>👋</div>
                <div>Caja: <strong>Activa</strong></div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-right-from-bracket"></i> Cerrar sesión   
                </a>
            </div>

        </header>

       


        <!-- ===== GRID DE PRODUCTOS ===== -->
        <div class="chart-container">

                
            

            <div class="products-header">

                

                <h3>Edite los Datos: </h3>


                <a href="direcciones.php" class="new-btn">
                    <i class="fas fa-reply"></i> Regresar  
                </a>

                

            </div>

            <!-- Mensaje -->
            <?php echo $mensaje; ?>

            <form method="POST" class="form-producto">

                <div class="form-group">
                <label>Calle:</label>
                <input type="text" name="calle" value="<?php echo $direccion['calle']; ?>" required>
                </div>

                <div class="form-group">
                <label>Número:</label>
                <input type="text" name="numero" value="<?php echo $direccion['numero']; ?>" required>
                </div>

                <div class="form-group">
                <label>Colonia:</label>
                <input type="text" name="colonia" value="<?php echo $direccion['colonia']; ?>" required>
                </div>

                <div class="form-group">
                <label>Municipio:</label>
                <input type="text" name="municipio" value="<?php echo $direccion['municipio']; ?>" required>
                </div>

                <div class="form-group">
                <label>Codigo Postal:</label>
                <input type="text" name="codigo_postal" value="<?php echo $direccion['codigo_postal']; ?>" required>
                </div>

                <div class="form-group">
                <label>Ciudad:</label>
                <input type="text" name="ciudad" value="<?php echo $direccion['ciudad']; ?>" required>
                </div>

                <div class="form-group">
                <label>Estado:</label>
                <input type="text" name="estado" value="<?php echo $direccion['estado']; ?>" required>
                </div>

                <div class="form-group">
                <label>Pais:</label>
                <input type="text" name="pais" value="<?php echo $direccion['pais']; ?>" required>
                </div>

                <div class="form-group full">
                <label>Referencia:</label>
                <input type="text" name="referencia" value="<?php echo $direccion['referencia']; ?>" required>
                </div>

                <div class="form-buttons">

                <button type="submit" name="guardar">
                <i class="fas fa-save"></i> Actualizar Dirección
                </button>

                <a href="direcciones.php">
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

<?php
$conn->close();
?>