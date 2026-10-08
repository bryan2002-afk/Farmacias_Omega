<?php
include("auth.php"); // protege la página
include("conexion.php"); // conexión a la BD

// ================================
// MENSAJE Y VALORES DEL FORMULARIO
// ================================
$mensaje = null;
$nombre_val = '';
$descripcion_val = '';
$prefijo_val = '';

// PROCESAR FORMULARIO
if (isset($_POST['guardar'])) {

    // 🔐 Sanitizar datos
    $nombre      = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
    $descripcion = htmlspecialchars(trim($_POST['descripcion']), ENT_QUOTES, 'UTF-8');
    $prefijo     = htmlspecialchars(trim($_POST['prefijo']), ENT_QUOTES, 'UTF-8');

    // Guardar valores para que el formulario los conserve en caso de error
    $nombre_val = $nombre;
    $descripcion_val = $descripcion;
    $prefijo_val = $prefijo;

    // ❌ Validar campos obligatorios
    if (empty($nombre) || empty($prefijo)) {
        $mensaje = [
            'tipo' => 'error',
            'texto' => "Los campos Nombre y Prefijo son obligatorios ❌"
        ];
    }
    // ❌ Validar longitudes máximas
    elseif (strlen($nombre) > 255 || strlen($prefijo) > 50 || strlen($descripcion) > 250) {
        $mensaje = [
            'tipo' => 'error',
            'texto' => "Nombre, Prefijo o Descripción demasiado largo ❌"
        ];
    }
    else {
        // ================================
        // EVITAR DUPLICADOS
        // ================================

        // Duplicado por nombre
        $checkNombre = $conn->prepare("SELECT id_categoria FROM categoria WHERE nombre = ?");
        $checkNombre->bind_param("s", $nombre);
        $checkNombre->execute();
        $checkNombre->store_result();
        if ($checkNombre->num_rows > 0) {
            $mensaje = [
                'tipo' => 'error',
                'texto' => "Ya existe una categoría con ese nombre ❌"
            ];
        }
        $checkNombre->close();

        // Duplicado por prefijo
        $checkPrefijo = $conn->prepare("SELECT id_categoria FROM categoria WHERE prefijo = ?");
        $checkPrefijo->bind_param("s", $prefijo);
        $checkPrefijo->execute();
        $checkPrefijo->store_result();
        if ($checkPrefijo->num_rows > 0) {
            $mensaje = [
                'tipo' => 'error',
                'texto' => "Ya existe una categoría con ese prefijo ❌"
            ];
        }
        $checkPrefijo->close();

        // ================================
        // INSERTAR EN LA BD SI NO HAY ERROR
        // ================================
        if (!$mensaje) {
            $sql = "INSERT INTO categoria (nombre, descripcion, prefijo) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $nombre, $descripcion, $prefijo);

            if ($stmt->execute()) {

                // ✅ Guardar mensaje en sesión y redirigir
                $_SESSION['mensaje'] = [
                    'tipo' => 'success',
                    'texto' => "Categoría guardada correctamente ☑️\nNombre: $nombre"
                ];

                header("Location: categorias.php");
                exit();

            } else {
                $mensaje = [
                    'tipo' => 'error',
                    'texto' => "Error al guardar categoría ❌"
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
    <title>Agregar Categoria CAJA - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="css/style_categorias_agregar.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_categorias_agregar.js"></script>
</head>



<body>

    <!-- Toast -->
    <?php if($mensaje): ?>
    <div id="toast" class="toast <?php echo $mensaje['tipo']; ?>" role="alert">
        <?php echo $mensaje['texto']; ?>
    </div>
    <?php else: ?>
        <div id="toast" class="toast"></div>
    <?php endif; ?>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const toastDiv = document.getElementById('toast');
            if(toastDiv && toastDiv.innerHTML.trim() !== '') {
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
                    <li><a href="productos_lista.php"><i class="fas fa-boxes"></i><span>Productos</span></a></li>
                    <li><a href="categorias.php"><i class="fas fa-user-cog"></i><span>Categorías</span></a></li>
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
                    <h1>Agregar Categoria - Caja POS</h1>
                    
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

                

                <h3>Ingrese los Datos: </h3>


                <a href="categorias.php" class="new-btn">
                    <i class="fas fa-reply"></i> Regresar  
                </a>

                

            </div>

            <!-- Mensaje -->
            

            <!-- Formulario con valores retenidos -->
            <form action="" method="POST" enctype="multipart/form-data" class="form-producto">

                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" required value="<?php echo htmlspecialchars($nombre_val); ?>">
                </div>    

                <div class="form-group">
                    <label>Prefijo:</label>
                    <input type="text" name="prefijo" required value="<?php echo htmlspecialchars($prefijo_val); ?>">
                </div>

                <div class="form-group full">
                    <label>Descripción:</label>
                    <textarea name="descripcion" rows="4"><?php echo htmlspecialchars($descripcion_val); ?></textarea>
                </div>

                <div class="form-buttons">
                    <button type="submit" name="guardar">
                        <i class="fas fa-save"></i> Guardar Categoria
                    </button>
                    <a href="categorias.php">
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