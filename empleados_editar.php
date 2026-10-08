<?php
include("auth.php"); // protege la página
include("conexion.php");

// ================= VALIDAR ID =================
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "ID no especificado ❌"
    ];
    header("Location: empleados.php");
    exit();
}

$id = intval($_GET['id']);

// ================= OBTENER EMPLEADO =================
$sql = "SELECT * FROM empleado WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en SELECT: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$empleado = $result->fetch_assoc();

$stmt->close();

if (!$empleado) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => "Empleado no encontrado ❌"
    ];
    header("Location: empleados.php");
    exit();
}

// ================= OBTENER DIRECCIONES =================
$direcciones = [];

$result_dir = $conn->query("
    SELECT id_direccion, calle, numero, colonia, codigo_postal
    FROM direccion
    WHERE activo = 1
    ORDER BY id_direccion DESC
");

if ($result_dir) {
    while ($row = $result_dir->fetch_assoc()) {
        $direcciones[] = $row;
    }
}

// ================= MENSAJE DESDE SESIÓN =================
$mensaje = null;

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

// ================= PROCESAR FORMULARIO =================
if (isset($_POST['guardar'])) {

    $curp         = trim($_POST['curp']);
    $nombre       = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
    $nombre_seg   = htmlspecialchars(trim($_POST['nombre_seg']), ENT_QUOTES, 'UTF-8');
    $apellido_p   = htmlspecialchars(trim($_POST['apellido_p']), ENT_QUOTES, 'UTF-8');
    $apellido_m   = htmlspecialchars(trim($_POST['apellido_m']), ENT_QUOTES, 'UTF-8');
    $telefono     = trim($_POST['telefono']);
    $correo       = filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL);
    $id_direccion = intval($_POST['id_direccion']);

    // ================= VALIDAR CORREO =================
    if (!$correo) {
        $_SESSION['mensaje'] = [
            'tipo' => 'error',
            'texto' => "Correo inválido ❌"
        ];
        header("Location: empleados.php");
        exit();
    }

    // ================= IMAGEN =================
    $imagen_nombre = $empleado['imagen'];

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {

        $archivo = $_FILES['imagen'];

        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $permitidas)) {

            $_SESSION['mensaje'] = [
                'tipo' => 'error',
                'texto' => "Formato de imagen no permitido ❌"
            ];

            header("Location: empleados.php");
            exit();
        }

        $carpeta = "ima_empleados/";

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        $nuevo_nombre = uniqid("img_") . "." . $ext;
        $destino = $carpeta . $nuevo_nombre;

        if (move_uploaded_file($archivo['tmp_name'], $destino)) {

            // eliminar imagen anterior
            if (!empty($empleado['imagen'])) {

                $anterior = $carpeta . basename($empleado['imagen']);

                if (file_exists($anterior)) {
                    unlink($anterior);
                }
            }

            $imagen_nombre = $nuevo_nombre;
        }
    }

    // ================= UPDATE =================
    $sql_update = "UPDATE empleado SET
        curp=?,
        nombre=?,
        nombre_seg=?,
        apellido_p=?,
        apellido_m=?,
        telefono=?,
        correo=?,
        id_direccion=?,
        imagen=?
        WHERE id_empleado=?";

    $stmt_update = $conn->prepare($sql_update);

    if (!$stmt_update) {
        die("Error en UPDATE: " . $conn->error);
    }

    $stmt_update->bind_param(
        "sssssssisi",
        $curp,
        $nombre,
        $nombre_seg,
        $apellido_p,
        $apellido_m,
        $telefono,
        $correo,
        $id_direccion,
        $imagen_nombre,
        $id
    );

    if ($stmt_update->execute()) {

        $_SESSION['mensaje'] = [
            'tipo' => 'success',
            'texto' => "Empleado actualizado correctamente ✏️"
        ];

    } else {

        $_SESSION['mensaje'] = [
            'tipo' => 'error',
            'texto' => "Error al actualizar ❌"
        ];
    }

    $stmt_update->close();

    header("Location: empleados.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empleados CAJA - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="css/style_empleados_agregar.css">
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
                    <h1>Editar Empleado - Caja POS</h1>
                    
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


                <a href="empleados.php" class="new-btn">
                    <i class="fas fa-reply"></i> Regresar  
                </a>

                

            </div>

            <!-- Mensaje -->
            <?php echo $mensaje; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="form-producto">

                <div class="form-group">
                    <label>CURP:</label>
                    <input type="text" name="curp" value="<?php echo $empleado['curp']; ?>" required>
                </div>    

                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" value="<?php echo $empleado['nombre']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Segundo Nombre:</label>
                    <input type="text" name="nombre_seg" value="<?php echo $empleado['nombre_seg']; ?>">
                </div>

                <div class="form-group">
                    <label>Apellido Paterno:</label>
                    <input type="text" name="apellido_p" value="<?php echo $empleado['apellido_p']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Apellido Materno:</label>
                    <input type="text" name="apellido_m" value="<?php echo $empleado['apellido_m']; ?>">
                </div>

                <div class="form-group">
                    <label>Telefono:</label>
                    <input type="text" name="telefono" value="<?php echo $empleado['telefono']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Correo:</label>
                    <input type="text" name="correo" value="<?php echo $empleado['correo']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Dirección:</label>

                    <select name="id_direccion" required>
                        <option value="">-- Seleccione Dirección --</option>

                        <?php foreach($direcciones as $dir): ?>
                            <option value="<?php echo $dir['id_direccion']; ?>"
                                <?php echo ($empleado['id_direccion'] == $dir['id_direccion']) ? 'selected' : ''; ?>>

                                <?php echo htmlspecialchars(
                                    $dir['calle'] . " #" . $dir['numero'] .
                                    ", Col. " . $dir['colonia'] .
                                    ", CP " . $dir['codigo_postal']
                                ); ?>

                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="form-group full">
                    <label>Imagen actual:</label><br>

                    <?php if ($empleado['imagen']) { ?>
                        <img src="ima_empleados/<?php echo $empleado['imagen']; ?>" 
                            alt="Imagen actual" 
                            style="max-width:150px; display:block; margin-bottom:10px;">
                    <?php } else { ?>
                        <p>Sin imagen</p>
                    <?php } ?>

                    <label>Cambiar imagen:</label>
                    <input type="file" name="imagen" accept="image/*" onchange="previewImage(event)">

                    <img id="preview" src="#" alt="Preview" style="max-width:150px; display:none; margin-top:10px;">
                </div>

                <div class="form-buttons">
                    <button type="submit" name="guardar">
                        <i class="fas fa-save"></i> Actualizar Empleado
                    </button>

                    <a href="empleados.php">
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