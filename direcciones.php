<?php
include("auth.php"); 
include("conexion.php");

// ================= MENSAJE =================
$mensaje = null;
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

// ================= BUSCAR (SEGURO) =================
$buscar = trim($_GET['buscar'] ?? "");

// ================= CONSULTA SEGURA =================
if ($buscar !== "") {

    $sql = "SELECT a.id_direccion, a.calle, a.numero, a.colonia, a.municipio, a.ciudad, a.estado, a.pais, a.codigo_postal, a.referencia
            FROM direccion a
            WHERE a.colonia LIKE ? 
            OR a.codigo_postal LIKE ? 
            OR a.calle LIKE ?
            ORDER BY a.id_direccion DESC";

    $stmt = $conn->prepare($sql);
    $param = "%$buscar%";
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();

} else {

    $sql = "SELECT a.id_direccion, a.calle, a.numero, a.colonia, a.municipio, a.ciudad, a.estado, a.pais, a.codigo_postal, a.referencia
            FROM direccion a
            ORDER BY a.id_direccion DESC";

    $result = $conn->query($sql);
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Direcciónes CAJA - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="css/style_direcciones.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_direcciones.js"></script>
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
            toastDiv.textContent = mensaje.texto;
            //toastDiv.innerHTML = mensaje.texto;
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
                    <!--<img src="img/1.png" alt="Perfil" class="avatar">-->
                    <img src="ima_usuarios/<?php echo $imagenUsuario; ?>" alt="Perfil" class="avatar">
                    <!--<span>Bryan</span>-->
                    <span><?php echo $nombreEmpleado; ?></span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list" >
                    <li><a href="mi_perfil.php"><i class="fas fa-user"></i><span>Mi Perfil</span></a></li>
                    <li><a href="configuracion.html"><i class="fas fa-cog"></i><span>Configuración</span></a></li>
                    <li><a href="logout.php"><i class="fas fa-right-from-bracket"></i><span>Cerrar Sesión</span></a></li>
                </ul>
            </li>
            
            <!--style="color:#2ecc71;-->

            <li><a href="dashboard.php"><i class="fas fa-th-large" ></i><span> Dashboard</span></a></li>
            <!--<li><a href="ventas.php"><i class="fas fa-shopping-cart"></i><span>Ventas</span></a></li>-->
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-shopping-cart" ></i>
                    <span>Ventas</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="ventas.php"><i class="fas fa-shopping-cart" ></i><span>Ventas</span></a></li>
                    <li><a href="clientes.php"><i class="fas fa-handshake"></i><span>Clientes</span></a></li>
                </ul>
            </li>
            <!--<li><a href="compras.php"><i class="fas fa-box"></i><span>Compras</span></a></li>--> 
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-box"></i>
                    <span>Compras</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="compras.php"><i class="fas fa-box"></i><span>Compras</span></a></li>
                    <li><a href="proveedores.php"><i class="fas fa-truck" ></i><span>Proveedores</span></a></li>
                </ul>
            </li>

            <li><a href="inventario.php"><i class="fas fa-clipboard-list"></i><span>Inventario</span></a></li>
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-boxes"></i>
                    <span>Productos</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="productos_lista.php"><i class="fas fa-box" ></i><span>Productos</span></a></li>
                    <li><a href="categorias.php"><i class="fas fa-user-cog" ></i><span>Categorías</span></a></li>
                </ul>
            </li>
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="empleados.php"><i class="fas fa-user-tie"></i><span>Empleados</span></a></li>
                    <li><a href="perfiles.php"><i class="fas fa-id-badge"></i><span>Perfiles</span></a></li>
                    <li><a href="usuarios.php"><i class="fas fa-user-cog" ></i><span>Usuarios</span></a></li>
                    <!--<li><a href="proveedores.php"><i class="fas fa-truck"></i><span>Proveedores</span></a></li>-->
                    <!--<li><a href="clientes.php"><i class="fas fa-handshake"></i><span>Clientes</span></a></li>
                    <li><a href="direcciones.php"><i class="fas fa-map-marker-alt"></i><span>Direcciónes</span></a></li>-->
                </ul>
            </li>
            <li><a href="reportes.php"><i class="fas fa-folder-open"></i><span>Reportes</span></a></li>
            <li><a href="direcciones.php"><i class="fas fa-map-marker-alt" style="color:#2ecc71"></i><span>Direcciónes</span></a></li>
            <li><a href="caja.php"><i class="fas fa-envelope"></i><span>Caja</span></a></li>
            
           
            
            

        </ul>       
    </div>

    
    <main class="contenido">
        
        <header class="dashboard-header">
    
            <!-- FILA SUPERIOR -->
            <div class="header-top">
                <div>
                    <h1>Direcciónes - Caja POS</h1>
                    
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

                

                <h3>Direcciónes Existentes: </h3>


                

                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="buscador" placeholder="Buscar por Codigo Postal, Calle, Colonia." onkeyup="filtrarTabla()">
                </div>

                <a href="direcciones_agregar.php" class="new-btn">
                    <i class="fas fa-plus"></i> Agregar dirección   
                </a>

                

            </div>

            <section class="products-grid">

                

                <table id="tablaDirecciones">
                    <thead>
                        <tr>
                            <th>Codigo Postal</th>
                            <th>Calle</th>
                            <th>Número</th>
                            <th>Colonia</th>
                            <th>Municipio</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>

                            <td><?= htmlspecialchars($row['codigo_postal']) ?></td>

                            <td><?= htmlspecialchars($row['calle']) ?></td>

                            <td><?= htmlspecialchars($row['numero']) ?></td>

                            <td><?= htmlspecialchars($row['colonia']) ?></td>

                            <td><?= htmlspecialchars($row['municipio']) ?></td>

                            <td><?= htmlspecialchars($row['estado']) ?></td>

                            

                        

                            <td>
                                <?php $id = (int)$row['id_direccion']; ?>

                               <!-- <a href="toggle_estado.php?id=<?= $id ?>&estado=<?= $row['estado'] ?>" class="btn-edit">
                                    🔄 Cambiar estado
                                </a>

                                <button class="btn-view"
                                onclick='verEmpleado(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                    <i class="fas fa-eye ojo-ver"></i> Ver
                                </button>--> 

                                <a href="direcciones_editar.php?id=<?= $id ?>" class="btn-edit">
                                    ✏️ Editar
                                </a>

                                <a href="direcciones_eliminar.php?id=<?= $id ?>" 
                                class="btn-delete"
                                onclick="return confirm('¿Eliminar la Dirección: <?= htmlspecialchars($row['calle']) ?>?')">
                                    🗑 Eliminar
                                </a>
                            </td>

                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
            </section>

            


        </div>


        

        
        <footer class="footer">

            <p class="copy">© 2026 Farmacias Omega Oaxaca - Todos los derechos reservados</p>

        </footer>


    </main>



    <!--Modal
    <div id="productModal" class="modal">
        <div class="modal-content">

            <div class="close-modal" onclick="cerrarModal()">×</div>

            <h2>Información del Empleado</h2>

            <div class="modal-info">
                <p><strong>Nombre:</strong> <span id="modalNombre"></span></p>
                <p><strong>Apellidos:</strong> <span id="modalApellido"></span></p>
                <p><strong>CURP:</strong> <span id="modalCurp"></span></p>
                <p><strong>Teléfono:</strong> <span id="modalTelefono"></span></p>
                <p><strong>Correo:</strong> <span id="modalCorreo"></span></p>
                <p><strong>Imagen:</strong><br> <span id="modalImagen"></span></p>
                
            </div>

        </div>
    </div>-->

   
        
        

</body>
</html>

<?php
$conn->close();
?>