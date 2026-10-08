<?php
include("auth.php"); 
include("conexion.php");

// ================= MENSAJE ==================
$mensaje = null;
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

// ================= BUSCAR (SEGURO) =================
$buscar = trim($_GET['buscar'] ?? "");

// ================= CONSULTA SEGURA =================
if ($buscar !== "") {

    $sql = "SELECT a.id_categoria, a.nombre, a.descripcion, a.prefijo, a.estado
            FROM categoria a
            WHERE a.nombre LIKE ? 
            OR a.descripcion LIKE ? 
            OR a.prefijo LIKE ?
            ORDER BY a.id_categoria DESC";

    $stmt = $conn->prepare($sql);
    $param = "%$buscar%";
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();

} else {

    $sql = "SELECT a.id_categoria, a.nombre, a.descripcion, a.prefijo, a.estado
            FROM categoria a
            ORDER BY a.id_categoria DESC";

    $result = $conn->query($sql);
}

//  suma de articulos existentes
$sqlTotal = "SELECT COUNT(*) AS total FROM articulo";
$resTotal = $conn->query($sqlTotal);

if ($resTotal && $filaTotal = $resTotal->fetch_assoc()) {
    $totalProductos = $filaTotal['total'];
}

// suma de artículos con stock menor o igual a 15
$sqlTotal = "SELECT COUNT(*) AS total 
             FROM articulo
             WHERE stock <= 15";

$resTotal = $conn->query($sqlTotal);

if ($resTotal && $filaTotal = $resTotal->fetch_assoc()) {
    $totalStock_Bajo = $filaTotal['total'];
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorias CAJA - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="css/style_categorias.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_categorias.js"></script>
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
                    <li><a href="categorias.php"><i class="fas fa-user-cog" style="color:#2ecc71"></i><span>Categorías</span></a></li>
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
                    <li><a href="usuarios.php"><i class="fas fa-user-cog"></i><span>Usuarios</span></a></li>
                    <!--<li><a href="proveedores.php"><i class="fas fa-truck"></i><span>Proveedores</span></a></li>-->
                    <!--<li><a href="clientes.php"><i class="fas fa-handshake"></i><span>Clientes</span></a></li>
                    <li><a href="direcciones.php"><i class="fas fa-map-marker-alt"></i><span>Direcciónes</span></a></li>-->
                </ul>
            </li>
            <li><a href="reportes.php"><i class="fas fa-folder-open"></i><span>Reportes</span></a></li>
            <li><a href="direcciones.php"><i class="fas fa-map-marker-alt"></i><span>Direcciónes</span></a></li>
            <li><a href="caja.php"><i class="fas fa-envelope"></i><span>Caja</span></a></li>
            
           
            
            

        </ul>      
    </div>

    
    <main class="contenido">
        
        <header class="dashboard-header">
    
            <!-- FILA SUPERIOR -->
            <div class="header-top">
                <div>
                    <h1>Categorias de Productos - Caja POS</h1>
                    
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

        <div class="dashboard-cards2">

            <div class="card2" onclick="window.location.href='productos_lista.php'">
                <div class="card2-icon2"><i class="fas fa-boxes"></i></div>
                <div>
                    <h3>Productos</h3>
                    <p><?= $totalProductos ?></p>
                </div>
            </div>

            <div class="card2" onclick="window.location.href='stock_bajo.php'">
                <div class="card2-icon2"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <h3>Stock Bajo</h3>
                    <p><?= $totalStock_Bajo ?> Producto´s</p>
                </div>
            </div>

           


        </div>


        <!-- ===== GRID DE PRODUCTOS ===== -->
        <div class="chart-container">

                
            

            <div class="products-header">

                

                <h3>Categorias Existentes: </h3>


                <!-- Cuadro de búsqueda 
                <input type="text" id="buscador" placeholder="Buscar por nombre o curp..." onkeyup="filtrarTabla()">--> 

                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="buscador" placeholder="Buscar por nombre o prefijo..." onkeyup="filtrarTabla()">
                </div>

                <a href="categorias_agregar.php" class="new-btn">
                    <i class="fas fa-plus"></i> Agregar categoria   
                </a>

                

            </div>

            <section class="products-grid">

                

                <table id="tablaCategorias">
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Descripción</th>
                            <th>Prefijo</th>
                            
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>

                            <?php $id = (int)$row['id_categoria']; ?>

                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <td><?= htmlspecialchars($row['descripcion']) ?></td>
                            <td><?= htmlspecialchars($row['prefijo']) ?></td>

                            <td>
                                <?php if ($row['estado'] == 1): ?>
                                    <span style="color: green; font-weight: bold;">Activo</span>
                                <?php else: ?>
                                    <span style="color: red; font-weight: bold;">Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php $id = (int)$row['id_categoria']; ?>

                                <a href="toggle_estado_categorias.php?id=<?= $id ?>&estado=<?= $row['estado'] ?>" class="btn-edit">
                                    🔄 Cambiar estado
                                </a>

                                

                                <a href="categorias_editar.php?id=<?= $id ?>" class="btn-edit">
                                    ✏️ Editar
                                </a>

                                <a href="categorias_eliminar.php?id=<?= $id ?>" 
                                class="btn-delete"
                                onclick="return confirm('¿Eliminar el empleado: <?= htmlspecialchars($row['nombre']) ?>?')">
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




   
        
        

</body>
</html>

<?php
$conn->close();
?>