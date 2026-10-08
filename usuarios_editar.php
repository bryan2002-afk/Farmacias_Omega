<?php
session_start();
include("auth.php");
include("conexion.php");

// ================= MENSAJE =================
$mensaje = [
    "tipo" => "",
    "texto" => ""
];

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

// ================= VALIDAR ID DE USUARIO =================
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "ID de usuario no proporcionado."
    ];
    header("Location: usuarios.php");
    exit();
}

$id_usuario = (int)$_GET['id'];

// ================= OBTENER DATOS DEL USUARIO =================
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['mensaje'] = [
        "tipo" => "error",
        "texto" => "Usuario no encontrado."
    ];
    header("Location: usuarios.php");
    exit();
}

$usuario = $result->fetch_assoc();
$stmt->close();

// ================= OBTENER EMPLEADOS =================
$empleados = [];
$result_emp = $conn->query("SELECT id_empleado, nombre FROM empleado ORDER BY nombre ASC");
if ($result_emp) {
    while ($row = $result_emp->fetch_assoc()) {
        $empleados[] = $row;
    }
}

// ================= OBTENER PERFILES =================
$perfiles = [];
$result_perf = $conn->query("SELECT id_perfil, nombre FROM perfil ORDER BY nombre ASC");
if ($result_perf) {
    while ($row = $result_perf->fetch_assoc()) {
        $perfiles[] = $row;
    }
}

// ================= PROCESAR FORMULARIO =================
if (isset($_POST['actualizar'])) {

    $usuario_nuevo = trim($_POST['usuario']);
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);
    $id_empleado = $_POST['id_empleado'];
    $id_perfil = $_POST['id_perfil'];

    // ================= VALIDACIÓN =================
    if (empty($usuario_nuevo) || empty($correo) || empty($id_empleado) || empty($id_perfil)) {
        $_SESSION['mensaje'] = [
            "tipo" => "error",
            "texto" => "Todos los campos son obligatorios (excepto contraseña)."
        ];
        header("Location: usuarios_editar.php?id=" . $id_usuario);
        exit();
    }

    // ================= VALIDAR CORREO ÚNICO =================
    $stmt_check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ? AND id_usuario != ?");
    $stmt_check->bind_param("si", $correo, $id_usuario);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $stmt_check->close();

    if ($result_check->num_rows > 0) {
        $_SESSION['mensaje'] = [
            "tipo" => "error",
            "texto" => "El correo ya está en uso."
        ];
        header("Location: usuarios_editar.php?id=" . $id_usuario);
        exit();
    }

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
            move_uploaded_file($archivo['tmp_name'], $destino);
        } else {
            $_SESSION['mensaje'] = [
                "tipo" => "error",
                "texto" => "Formato de imagen no permitido."
            ];
            header("Location: usuarios_editar.php?id=" . $id_usuario);
            exit();
        }
    }

    // ================= CONTRASEÑA =================
    if (!empty($password)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if ($imagen_nombre) {
            $sql = "UPDATE usuarios 
                    SET usuario=?, correo=?, password=?, id_empleado=?, id_perfil=?, imagen=? 
                    WHERE id_usuario=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssissi", $usuario_nuevo, $correo, $passwordHash, $id_empleado, $id_perfil, $imagen_nombre, $id_usuario);
        } else {
            $sql = "UPDATE usuarios 
                    SET usuario=?, correo=?, password=?, id_empleado=?, id_perfil=? 
                    WHERE id_usuario=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssisi", $usuario_nuevo, $correo, $passwordHash, $id_empleado, $id_perfil, $id_usuario);
        }
    } else {
        if ($imagen_nombre) {
            $sql = "UPDATE usuarios 
                    SET usuario=?, correo=?, id_empleado=?, id_perfil=?, imagen=? 
                    WHERE id_usuario=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssissi", $usuario_nuevo, $correo, $id_empleado, $id_perfil, $imagen_nombre, $id_usuario);
        } else {
            $sql = "UPDATE usuarios 
                    SET usuario=?, correo=?, id_empleado=?, id_perfil=? 
                    WHERE id_usuario=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssisi", $usuario_nuevo, $correo, $id_empleado, $id_perfil, $id_usuario);
        }
    }

    // ================= EJECUTAR =================
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = [
            "tipo" => "success",
            "texto" => "Usuario actualizado correctamente."
        ];
    } else {
        $_SESSION['mensaje'] = [
            "tipo" => "error",
            "texto" => "Error al actualizar: " . $stmt->error
        ];
    }

    $stmt->close();
    header("Location: usuarios.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuarios CAJA - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="css/style_usuarios_editar.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_usuarios_editar.js"></script>
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
                    <h1>Editar Usuario - Caja POS</h1>
                    
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


                <a href="usuarios.php" class="new-btn">
                    <i class="fas fa-reply"></i> Regresar  
                </a>

                

            </div>

            <!-- Mensaje -->
            
            

            <form action="" method="POST" enctype="multipart/form-data" class="form-producto">

                <!-- ID oculto para actualizar -->
                <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">

                <div class="form-group">
                    <label>Usuario:</label>
                    <input type="text" name="usuario" required value="<?= htmlspecialchars($usuario['usuario']) ?>">
                </div>    

                <div class="form-group">
                    <label>Correo:</label>
                    <input type="email" name="correo" required value="<?= htmlspecialchars($usuario['correo']) ?>">
                </div>

                <div class="form-group">
                    <label>Contraseña (opcional):</label>
                    <input type="password" name="password" placeholder="Dejar vacío para no cambiar">
                </div>

                <div class="form-group">
                    <label>Empleado:</label>
                    <select name="id_empleado" required>
                        <option value="">-- Seleccione Empleado --</option>
                        <?php foreach($empleados as $emp): ?>
                            <option value="<?= $emp['id_empleado'] ?>" <?= $usuario['id_empleado'] == $emp['id_empleado'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($emp['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Perfil:</label>
                    <select name="id_perfil" required>
                        <option value="">-- Seleccione Perfil --</option>
                        <?php foreach($perfiles as $perf): ?>
                            <option value="<?= $perf['id_perfil'] ?>" <?= $usuario['id_perfil'] == $perf['id_perfil'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($perf['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                

                <div class="form-group ">
                    <label>Imagen actual:</label><br>

                    <?php if ($usuario['imagen']) { ?>
                        <img src="ima_usuarios/<?php echo $usuario['imagen']; ?>" 
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
                    <button type="submit" name="actualizar">
                        <i class="fas fa-save"></i> Actualizar Usuario
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