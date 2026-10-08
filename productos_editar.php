<?php
session_start();
include("auth.php");
include("conexion.php");

/* ================= MENSAJE ================= */
$mensaje = null;

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

/* ================= VALIDAR ID ================= */
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'Producto no especificado ❌'
    ];

    header("Location: productos_lista.php");
    exit();
}

$id = intval($_GET['id']);

/* ================= OBTENER PRODUCTO ================= */
$sql_producto = "SELECT * FROM articulo WHERE id_articulo = ?";
$stmt_producto = $conn->prepare($sql_producto);

if (!$stmt_producto) {
    die("Error en SELECT: " . $conn->error);
}

$stmt_producto->bind_param("i", $id);
$stmt_producto->execute();

$result_producto = $stmt_producto->get_result();
$producto = $result_producto->fetch_assoc();

if (!$producto) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'Producto no encontrado ❌'
    ];

    header("Location: productos_lista.php");
    exit();
}

/* ================= OBTENER CATEGORÍAS ================= */
$categorias = [];

$sql_cat = "SELECT id_categoria, nombre FROM categoria ORDER BY nombre ASC";
$result_cat = $conn->query($sql_cat);

while ($row = $result_cat->fetch_assoc()) {
    $categorias[] = $row;
}

/* ================= ACTUALIZAR PRODUCTO ================= */
if (isset($_POST['guardar'])) {

    $nombre       = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
    $descripcion  = htmlspecialchars(trim($_POST['descripcion']), ENT_QUOTES, 'UTF-8');
    $precio       = floatval($_POST['precio']);
    $stock        = intval($_POST['stock']);
    $id_categoria = intval($_POST['id_categoria']);
    $estado       = intval($_POST['estado']);

    $imagen_nombre = $producto['imagen'];

    /* ================= SUBIR IMAGEN ================= */
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {

        $archivo = $_FILES['imagen'];

        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $permitidas)) {

            $_SESSION['mensaje'] = [
                'tipo' => 'error',
                'texto' => 'Formato de imagen no permitido ❌'
            ];

            header("Location: productos_lista.php");
            exit();
        }

        $carpeta = "ima_productos/";

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        $nuevo_nombre = uniqid("img_") . "." . $ext;
        $destino = $carpeta . $nuevo_nombre;

        if (move_uploaded_file($archivo['tmp_name'], $destino)) {

            /* borrar imagen anterior */
            if (!empty($producto['imagen'])) {

                $ruta_old = $carpeta . basename($producto['imagen']);

                if (file_exists($ruta_old)) {
                    unlink($ruta_old);
                }
            }

            $imagen_nombre = $nuevo_nombre;
        }
    }

    /* ================= UPDATE ================= */
    $sql = "UPDATE articulo 
            SET nombre=?, descripcion=?, precio=?, stock=?, id_categoria=?, imagen=?, estado=?
            WHERE id_articulo=?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error en UPDATE: " . $conn->error);
    }

    $stmt->bind_param(
        "ssdiisii",
        $nombre,
        $descripcion,
        $precio,
        $stock,
        $id_categoria,
        $imagen_nombre,
        $estado,
        $id
    );

    if ($stmt->execute()) {

        $_SESSION['mensaje'] = [
            'tipo' => 'success',
            'texto' => "Producto actualizado correctamente ☑️ Nombre: $nombre"
        ];

    } else {

        $_SESSION['mensaje'] = [
            'tipo' => 'error',
            'texto' => 'Error al actualizar ❌'
        ];
    }

    $stmt->close();

    header("Location: productos_lista.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Productos CAJA - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="css/style_productos_agregar.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_productos_editar.js"></script>
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
                    <h1>Editar Producto - Caja POS</h1>
                    
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


                <a href="productos_lista.php" class="new-btn">
                    <i class="fas fa-reply"></i> Regresar  
                </a>

                

            </div>

            <!-- Mensaje -->
            <?php echo $mensaje; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="form-producto">

                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Precio:</label>
                    <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Stock:</label>
                    <input type="number" name="stock" value="<?= $producto['stock'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Categoría:</label>
                    <select name="id_categoria" required>
                        <option value="">-- Seleccione categoría --</option>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?= $cat['id_categoria'] ?>"
                                <?= $producto['id_categoria'] == $cat['id_categoria'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Estado:</label>
                    <select name="estado" required>
                        <option value="1" <?= $producto['estado'] == 1 ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= $producto['estado'] == 0 ? 'selected' : '' ?>>No Activo</option>
                    </select>
                </div>

                <div class="form-group ">
                    <label>Descripción:</label>
                    <textarea name="descripcion"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                </div>

                
                <div class="form-group full">
                    <label>Imagen actual:</label><br>

                    <?php if ($producto['imagen']) { ?>
                        <img src="ima_productos/<?php echo $producto['imagen']; ?>" 
                            alt="Imagen actual" 
                            style="max-width:150px; display:block; margin-bottom:10px;">
                    <?php } else { ?>
                        <p>Sin imagen</p>
                    <?php } ?>

                    <label>Cambiar imagen:</label>
                    <input type="file" name="imagen" accept="image/*" onchange="previewImage(event)">
                    <img id="preview" style="display:none; max-width:150px;">

                    <!--<img id="preview" src="#" alt="Preview" style="max-width:150px; display:none; margin-top:10px;">-->
                </div>

                <div class="form-buttons">
                    <button type="submit" name="guardar">
                        <i class="fas fa-save"></i> Actualizar Producto
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