<?php
include("auth.php");
include("conexion.php");

// ================================
// OBTENER ID
// ================================
if (!isset($_GET['id'])) {
    header("Location: clientes.php");
    exit();
}

$id_cliente = intval($_GET['id']);

// ================================
// MENSAJE Y VALORES
// ================================
$mensaje = null;

$nombre_val = '';
$apellido_val = '';
$telefono_val = '';
$correo_val = '';
$estado_val = '';

// ================================
// CARGAR DATOS EXISTENTES
// ================================
$stmt = $conn->prepare("SELECT nombre, apellido, telefono, correo, estado FROM cliente WHERE id_cliente = ?");
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();

    $nombre_val = $data['nombre'];
    $apellido_val = $data['apellido'];
    $telefono_val = $data['telefono'];
    $correo_val = $data['correo'];
    $estado_val = $data['estado'];
    

} else {
    header("Location: clientes.php");
    exit();
}
$stmt->close();

// ================================
// PROCESAR FORMULARIO
// ================================
if (isset($_POST['guardar'])) {

    $nombre   = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
    $apellido = htmlspecialchars(trim($_POST['apellido']), ENT_QUOTES, 'UTF-8');
    $telefono = htmlspecialchars(trim($_POST['telefono']), ENT_QUOTES, 'UTF-8');
    $correo   = htmlspecialchars(trim($_POST['correo']), ENT_QUOTES, 'UTF-8');
    $estado   = htmlspecialchars(trim($_POST['estado']), ENT_QUOTES, 'UTF-8');

    // mantener valores en pantalla
    $nombre_val = $nombre;
    $apellido_val = $apellido;
    $telefono_val = $telefono;
    $correo_val = $correo;
    $estado_val = $estado;

    // ================================
    // VALIDACIONES BÁSICAS
    // ================================
    if (empty($nombre) || empty($telefono) || empty($correo)) {

        $mensaje = [
            'tipo' => 'error',
            'texto' => "Nombre, Teléfono y Correo son obligatorios ❌"
        ];

    } else {

        // ================================
        // VALIDAR TELÉFONO DUPLICADO
        // ================================
        $checkTel = $conn->prepare("
            SELECT id_cliente 
            FROM cliente 
            WHERE telefono = ? AND id_cliente != ?
        ");
        $checkTel->bind_param("si", $telefono, $id_cliente);
        $checkTel->execute();
        $checkTel->store_result();

        if ($checkTel->num_rows > 0) {

            $mensaje = [
                'tipo' => 'error',
                'texto' => "❌ El teléfono ya existe"
            ];
        }
        $checkTel->close();

        // ================================
        // VALIDAR CORREO DUPLICADO
        // ================================
        $checkCorreo = $conn->prepare("
            SELECT id_cliente 
            FROM cliente 
            WHERE correo = ? AND id_cliente != ?
        ");
        $checkCorreo->bind_param("si", $correo, $id_cliente);
        $checkCorreo->execute();
        $checkCorreo->store_result();

        if ($checkCorreo->num_rows > 0) {

            $mensaje = [
                'tipo' => 'error',
                'texto' => "❌ El correo ya existe"
            ];
        }
        $checkCorreo->close();

        // ================================
        // ACTUALIZAR
        // ================================
        if (!$mensaje) {

            $sql = "UPDATE cliente 
                    SET nombre = ?, apellido = ?, telefono = ?, correo = ?, estado = ?
                    WHERE id_cliente = ?";

            $stmt = $conn->prepare($sql);

            $estado = (int) $_POST['estado'];

            $stmt->bind_param("ssssii", $nombre, $apellido, $telefono, $correo, $estado, $id_cliente);

            if ($stmt->execute()) {

                $_SESSION['mensaje'] = [
                    'tipo' => 'success',
                    'texto' => "Cliente actualizado correctamente ☑️\nNombre: $nombre"
                ];

                header("Location: clientes.php");
                exit();

            } else {

                $mensaje = [
                    'tipo' => 'error',
                    'texto' => "Error al actualizar cliente ❌"
                ];
            }

            $stmt->close();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Clientes CAJA - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="css/style_clientes_agregar.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_clientes_agregar.js"></script>
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
                    <h1>Editar Cliente - Caja POS</h1>
                    
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


                <a href="clientes.php" class="new-btn">
                    <i class="fas fa-reply"></i> Regresar  
                </a>

                

            </div>

            <!-- Mensaje 
            <?php if ($mensaje): ?>
                <div class="<?= $mensaje['tipo']; ?>">
                    <?= $mensaje['texto']; ?>
                </div>
            <?php endif; ?>-->



           
            <form action="" method="POST" enctype="multipart/form-data" class="form-producto">

                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" required
                        value="<?= $nombre_val ?>">
                </div>

                <div class="form-group">
                    <label>Apellido:</label>
                    <input type="text" name="apellido"
                        value="<?= $apellido_val ?>">
                </div>

                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" required
                        value="<?= $telefono_val ?>">
                </div>

                <div class="form-group">
                    <label>Correo:</label>
                    <input type="text" name="correo" required
                        value="<?= $correo_val ?>">
                </div>

                <div class="form-group">
                    <label>Estado:</label>

                    <select name="estado" required>
                        <option value="1" <?= $estado_val == 1 ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= $estado_val == 0 ? 'selected' : '' ?>>No Activo</option>
                    </select>

                </div>

                <div class="form-buttons">
                    <button type="submit" name="guardar">
                        <i class="fas fa-save"></i> Actualizar Cliente
                    </button>

                    <a href="clientes.php">
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