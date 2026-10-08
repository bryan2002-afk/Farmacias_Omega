<?php
session_start(); // 🔥 ESTO TE FALTA
include("auth.php");
include("conexion.php");

// ================================
// MENSAJE Y VALORES DEL FORMULARIO
// ================================
/*$mensaje = null;
$nombre_val = '';
$descripcion_val = '';
$prefijo_val = '';*/
// ================= MENSAJE ==================
$mensaje = null;
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

/**/ 
// OBTENER CATEGORÍAS PARA EL SELECT
$categorias = [];
$result_cat = $conn->query("SELECT id_categoria, nombre FROM categoria ORDER BY nombre ASC");
if ($result_cat) {
    while ($row = $result_cat->fetch_assoc()) {
        $categorias[] = $row;
    }
}

$mensaje = ""; // Aquí guardaremos el mensaje de éxito o error

// PROCESAR FORMULARIO
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $id_categoria = $_POST['id_categoria'];

    // PROCESAR IMAGEN
    $imagen_nombre = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $archivo = $_FILES['imagen'];
        $nombre_original = $archivo['name'];
        $ext = pathinfo($nombre_original, PATHINFO_EXTENSION);

        $carpeta = 'ima_productos/';
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $imagen_nombre = uniqid('img_') . '.' . $ext;
        $destino = $carpeta . $imagen_nombre;

        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            $mensaje = "<p style='color:red;'>Error al subir la imagen.</p>";
            $imagen_nombre = null;
        }
    }

    // GENERAR CÓDIGO ÚNICO (ART- + timestamp)
    //$codigo = 'ART-' . time();
    // OBTENER PREFIJO DE LA CATEGORÍA
    $sql_prefijo = "SELECT prefijo FROM categoria WHERE id_categoria = ?";
    $stmt_prefijo = $conn->prepare($sql_prefijo);
    $stmt_prefijo->bind_param("i", $id_categoria);
    $stmt_prefijo->execute();
    $result_prefijo = $stmt_prefijo->get_result();
    $row_prefijo = $result_prefijo->fetch_assoc();

    $prefijo = $row_prefijo['prefijo'];

    $stmt_prefijo->close();

    // GENERAR CÓDIGO ÚNICO DEL PRODUCTO
    //$codigo = $prefijo . '-' . time();
    $codigo = $prefijo . '-' . strtoupper(substr(uniqid(), -5));

    // INSERTAR EN LA BASE DE DATOS
    $sql = "INSERT INTO articulo (codigo, nombre, descripcion, precio, stock, id_categoria, imagen, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdiss", $codigo, $nombre, $descripcion, $precio, $stock, $id_categoria, $imagen_nombre);

    if ($stmt->execute()) {
        // ✅ Guardar mensaje en sesión como array
        $_SESSION['mensaje'] = [
            'tipo' => 'success',
            'texto' => "Producto Guardado Correctamente ☑️\nNombre: $nombre "
        ];
        /*header("Location: productos_lista.php"); // redirige
        exit();*/
    } else {
        $_SESSION['mensaje'] = [
            'tipo' => 'error',
            'texto' => "Error al guardar el producto ❌ " . $conn->error
        ];
        /*header("Location: productos_lista.php"); // redirige
        exit();*/
    }

    $stmt->close();
    // 🔄 Redirección segura
    header("Location: productos_lista.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Productos CAJA - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="css/style_productos_agregar.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_productos_agregar.js"></script>
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
                    <h1>Agregar Producto - Caja POS</h1>
                    
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


                <a href="productos_lista.php" class="new-btn">
                    <i class="fas fa-reply"></i> Regresar  
                </a>

                

            </div>

            <!-- Mensaje -->
            <?php echo $mensaje; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="form-producto">

                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" required>
                </div>

                <div class="form-group">
                    <label>Precio:</label>
                    <input type="number" step="0.01" name="precio" required>
                </div>

                <div class="form-group">
                    <label>Stock:</label>
                    <input type="number" name="stock" required>
                </div>

                <div class="form-group">
                    <label>Categoría:</label>
                    <select name="id_categoria" required>
                        <option value="">-- Seleccione categoría --</option>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>">
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group ">
                    <label>Descripción:</label>
                    <textarea name="descripcion"></textarea>
                </div>

                <div class="form-group ">
                    <label>Imagen:</label>
                    <input type="file" name="imagen" accept="image/*" onchange="previewImage(event)">
                    <img id="preview" src="#" alt="Preview">
                </div>

                <div class="form-buttons">
                    <button type="submit" name="guardar">
                        <i class="fas fa-save"></i> Guardar Producto
                    </button>

                    <a href="productos_lista.php">
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