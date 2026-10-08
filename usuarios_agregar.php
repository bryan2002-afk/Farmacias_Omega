<?php
include("auth.php");
include("conexion.php");

// ================= MENSAJE =================
$mensaje = null;
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

// OBTENER empleados PARA EL SELECTt
$empleados = []; 
$result_cat = $conn->query("SELECT id_empleado, nombre FROM empleado ORDER BY nombre ASC"); 
if ($result_cat) { 
    while ($row = $result_cat->fetch_assoc()) { 
        $empleados[] = $row; 
    } 
} 

// OBTENER perfiles PARA EL SELECT 
$perfiles = []; 
$result_cat = $conn->query("SELECT id_perfil, nombre FROM perfil ORDER BY nombre ASC"); 
if ($result_cat) { 
    while ($row = $result_cat->fetch_assoc()) { 
        $perfiles[] = $row; 
    } 
}

// ================= PROCESAR FORMULARIO =================
if (isset($_POST['guardar'])) {

    $usuario = trim($_POST['usuario']);
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);
    $id_empleado = $_POST['id_empleado'];
    $id_perfil = $_POST['id_perfil'];

    // ================= VALIDACIÓN =================
    if (empty($usuario) || empty($correo) || empty($password) || empty($id_empleado) || empty($id_perfil)) {
        $_SESSION['mensaje'] = [
            "tipo" => "error",
            "texto" => "Todos los campos son obligatorios."
        ];
        header("Location: usuarios_agregar.php");
        exit();
    }

    // ================= VALIDAR CORREO ÚNICO =================
    $stmt_check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
    $stmt_check->bind_param("s", $correo);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $_SESSION['mensaje'] = [
            "tipo" => "error",
            "texto" => "El correo ya está registrado."
        ];
        header("Location: usuarios_agregar.php");
        exit();
    }

    // ================= ENCRIPTAR PASSWORD =================
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // ================= IMAGEN =================
    $imagen_nombre = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {

        $archivo = $_FILES['imagen'];
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $permitidas)) {

            $carpeta = 'ima_usuarios/';
            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            $imagen_nombre = uniqid('img_') . '.' . $ext;
            $destino = $carpeta . $imagen_nombre;

            if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
                $_SESSION['mensaje'] = [
                    "tipo" => "error",
                    "texto" => "Error al subir la imagen."
                ];
                header("Location: usuarios_agregar.php");
                exit();
            }

        } else {
            $_SESSION['mensaje'] = [
                "tipo" => "error",
                "texto" => "Formato de imagen no permitido."
            ];
            header("Location: usuarios_agregar.php");
            exit();
        }
    }

    // ================= INSERT =================
    $sql = "INSERT INTO usuarios 
            (usuario, correo, password, id_empleado, id_perfil, imagen, estado)
            VALUES (?, ?, ?, ?, ?, ?, 1)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssiss",
        $usuario,
        $correo,
        $passwordHash,
        $id_empleado,
        $id_perfil,
        $imagen_nombre
    );

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = [
            "tipo" => "success",
            "texto" => "Usuario registrado correctamente."
        ];
    } else {
        $_SESSION['mensaje'] = [
            "tipo" => "error",
            "texto" => "Error al guardar: " . $stmt->error
        ];
    }

    header("Location: usuarios.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Usuarios CAJA - Farmacias Omega Oaxaca </title>
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
                    <h1>Agregar Usuario - Caja POS</h1>
                    
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


                <a href="usuarios.php" class="new-btn">
                    <i class="fas fa-reply"></i> Regresar  
                </a>

                

            </div>

            <!-- Mensaje -->
            <?php echo $mensaje; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="form-producto">

                <div class="form-group">
                    <label>Usuario:</label>
                    <input type="text" name="usuario" required>
                </div>    

                <div class="form-group">
                    <label>Correo:</label>
                    <input type="text" name="correo" required>
                </div>

                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Empleado:</label>
                    <select name="id_empleado" required>
                        <option value="">-- Seleccione Empleado --</option>
                        <?php foreach($empleados as $cat): ?>
                            <option value="<?php echo $cat['id_empleado']; ?>">
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Perfil:</label>
                    <select name="id_perfil" required>
                        <option value="">-- Seleccione Perfil --</option>
                        <?php foreach($perfiles as $cat): ?>
                            <option value="<?php echo $cat['id_perfil']; ?>">
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

               <div class="form-group ">
                    <label>Imagen:</label>
                    <input type="file" name="imagen" accept="image/*" onchange="previewImage(event)">
                    <img id="preview" src="#" alt="Preview">
                </div>

                


                <div class="form-buttons">
                    <button type="submit" name="guardar">
                        <i class="fas fa-save"></i> Guardar Usuario
                    </button>

                    <a href="usuarios.php">
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