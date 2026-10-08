<?php
include("auth.php");
include("conexion.php");

// =====================================================
// 📩 MENSAJE DESDE SESIÓN
// =====================================================
$mensaje = null;
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}


// =====================================================
// 💾 RECUPERAR DATOS DEL FORMULARIO (SI HUBO ERROR)
// =====================================================
$form = $_SESSION['form_data'] ?? null;
unset($_SESSION['form_data']);



// =====================================================
// 📥 PROCESAR FORMULARIO
// =====================================================
if (isset($_POST['guardar'])) {

    // 🔐 Sanitizar datos
    $nombre   = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
    $apellido = htmlspecialchars(trim($_POST['apellido']), ENT_QUOTES, 'UTF-8');
    $telefono = htmlspecialchars(trim($_POST['telefono']), ENT_QUOTES, 'UTF-8');
    $correo   = htmlspecialchars(trim($_POST['correo']), ENT_QUOTES, 'UTF-8');

    $error = false;

    // =====================================================
    // 🔎 VALIDAR TELÉFONO DUPLICADO
    // =====================================================
    $sqlTel = "SELECT id_cliente FROM cliente WHERE telefono = ?";
    $stmtTel = $conn->prepare($sqlTel);
    $stmtTel->bind_param("s", $telefono);
    $stmtTel->execute();
    $resultTel = $stmtTel->get_result();

    if ($resultTel->num_rows > 0) {

        $_SESSION['mensaje'] = [
            'tipo' => 'error',
            'texto' => "❌ El número de teléfono ya existe."
        ];

        $error = true;
    }

    $stmtTel->close();


    // =====================================================
    // 🔎 VALIDAR CORREO DUPLICADO
    // =====================================================
    $sqlCorreo = "SELECT id_cliente FROM cliente WHERE correo = ?";
    $stmtCorreo = $conn->prepare($sqlCorreo);
    $stmtCorreo->bind_param("s", $correo);
    $stmtCorreo->execute();
    $resultCorreo = $stmtCorreo->get_result();

    if ($resultCorreo->num_rows > 0) {

        $_SESSION['mensaje'] = [
            'tipo' => 'error',
            'texto' => "❌ El correo ya existe."
        ];

        $error = true;
    }

    $stmtCorreo->close();


    // =====================================================
    // 🛑 SI HAY ERROR NO INSERTA
    // =====================================================
    /*if ($error) {
        header("Location: clientes_agregar.php");
        exit();
    }*/
    if ($error) {

        // 💾 Guardar datos para no perderlos en el formulario
        $_SESSION['form_data'] = [
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'telefono' => $telefono,
            'correo'   => $correo
        ];

        header("Location: clientes_agregar.php");
        exit();
    }


    // =====================================================
    // 🗄️ INSERTAR EN BD
    // =====================================================
    try {

        $sql = "INSERT INTO cliente (nombre, apellido, telefono, correo)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $apellido, $telefono, $correo);
        $stmt->execute();

        $_SESSION['mensaje'] = [
            'tipo' => 'success',
            'texto' => "Cliente guardado correctamente ☑️"
        ];

        $stmt->close();

        header("Location: clientes.php");
        exit();


    } catch (mysqli_sql_exception $e) {

        // =====================================================
        // ❌ ERROR DE BASE DE DATOS
        // =====================================================
        $_SESSION['mensaje'] = [
            'tipo' => 'error',
            'texto' => "❌ Error en la base de datos"
        ];

        header("Location: clientes_agregar.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Clientes CAJA - Farmacias Omega Oaxaca </title>
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
                    <h1>Agregar Cliente - Caja POS</h1>
                    
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



            <form action="" method="POST" class="form-producto">

                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" required
                        value="<?= $form['nombre'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label>Apellido:</label>
                    <input type="text" name="apellido"
                        value="<?= $form['apellido'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label>Telefono:</label>
                    <input type="text" name="telefono" required
                        value="<?= $form['telefono'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label>Correo:</label>
                    <input type="text" name="correo" required
                        value="<?= $form['correo'] ?? '' ?>">
                </div>

                <div class="form-buttons">
                    <button type="submit" name="guardar">
                        <i class="fas fa-save"></i> Guardar Cliente
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