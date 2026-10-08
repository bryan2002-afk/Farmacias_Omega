<?php
session_start(); // 🔥 ESTO TE FALTA
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

// ================= CONSULTA =================
$sql = "SELECT a.id_articulo, a.codigo , a.nombre, a.descripcion, a.precio, a.estado, a.stock,
               e.nombre AS categoria, /*p.nombre AS perfil,*/ a.imagen
        FROM articulo a
        LEFT JOIN categoria e ON a.id_categoria = e.id_categoria
        /*LEFT JOIN perfil p ON a.id_perfil = p.id_perfil*/";

// ================= SI HAY BÚSQUEDA =================
if ($buscar !== "") {

    $sql .= " WHERE a.codigo LIKE ?
              OR a.nombre LIKE ?
              OR e.nombre LIKE ?";

    $stmt = $conn->prepare($sql);

    $param = "%$buscar%";
    $stmt->bind_param("sss", $param, $param, $param);

    $stmt->execute();
    $result = $stmt->get_result();

} else {

    // Sin búsqueda: solo ordenamos
    $sql .= " ORDER BY a.id_articulo DESC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos Lista CAJA - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="css/style_productos_lista.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_productos_lista.js"></script>
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
                    <li><a href="productos_lista.php"><i class="fas fa-id-badge"></i><span>Lista Productos</span></a></li>
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
                    <h1>Lista de Productos - Caja POS</h1>
                    
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

                

                <h3>Productos Existentes: </h3>


             

                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="buscador" placeholder="Buscar producto..." onkeyup="filtrarTabla()">
                </div>

                <a href="productos_agregar.php" class="new-btn">
                    <i class="fas fa-plus"></i> Agregar producto   
                </a>

            </div>

            <!-- Sección de los productos o de section -->

            <!--<div class="productos-cartas" id="tablaArticulos">-->
            <div class="productos-cartas">

                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <div class="producto-card">
                            

                            <!-- BOTONES -->
                            <div class="acciones-cartas">
                                <?php $id = (int)$row['id_articulo']; ?>

                               <!-- <button class="ver-btn">
                                    onclick='verArticulo(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                    <i class="fas fa-eye"></i>
                                </button>  -->
                                <button class="ver-btn"
                                    onclick='verArticulo(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                    <i class="fas fa-eye"></i>
                                </button>

                                <!--<a href="toggle_estado_productos.php?id=<?= $id ?>&estado=<?= $row['estado'] ?>" class="btn-edit">
                                    <i class="fas fa-exchange-alt"></i>
                                </a>--> 
                                
                                <a href="toggle_estado_productos.php?id=<?= $id ?>&estado=<?= $row['estado'] ?>" 
                                    class="acciones-btn cambiar">
                                    <i class="fas fa-exchange-alt"></i>
                                </a>

                                <button class="editar">
                                    <i class="fas fa-edit"></i>  
                                </button>
                                <!--<button class="eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>--> 
                                <a href="productos_eliminar.php?id=<?= $id ?>" 
                                class="eliminar"
                                onclick="return confirm('¿Eliminar el Articulo: <?= htmlspecialchars($row['nombre']) ?>?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>

                            <!-- IMAGEN -->
                            <div class="card-header">
                                <?php if (!empty($row['imagen'])): ?>
                                    <img src="ima_productos/<?= htmlspecialchars($row['imagen']) ?>">
                                <?php else: ?>
                                    <span>Sin imagen</span>
                                <?php endif; ?>
                            </div>

                            <!-- CONTENIDO -->
                            <div class="card-content">

                                <h4 class="card-title">
                                    <?= htmlspecialchars($row['nombre']) ?>
                                </h4><br>

                              

                                <p class="card-excerpt">
                                    <strong>Código:</strong> <?= htmlspecialchars($row['codigo']) ?><br>
                                    <strong>Categoría:</strong> <?= htmlspecialchars($row['categoria']) ?><br>
                                    <strong>Precio: </strong><strong style="color: #57c5d3;">$ <?= htmlspecialchars($row['precio']) ?></strong><br><br>

                                    <strong style="color: #777; ">Estado: </strong>
                                        <small>
                                            <?php if ($row['estado'] == 1): ?>
                                                <span style="color: #27ae60; font-weight: bold; font-size: 16px">Activo</span>
                                            <?php else: ?>
                                                <span style="color: red; font-weight: bold; font-size: 16px;">Inactivo</span>
                                            <?php endif; ?>
                                        </small>
                                    
                                </p>


                                <!--<div class="author">

                                    <div class="profile-img-opciones">
                                        <img src="img/precio3.png" alt="Perfil">
                                    </div>

                                    <div class="author-info">
                                        <strong>$ <?= htmlspecialchars($row['precio']) ?></strong>
                                    </div>

                                </div>-->

                            </div>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <p>No hay productos registrados.</p>

                <?php endif; ?>

            </div>

            


        </div>


        

        
        <footer class="footer">

            <p class="copy">© 2026 Farmacias Omega Oaxaca - Todos los derechos reservados</p>

        </footer>


    </main>



    <!-- Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">

            <div class="close-modal" onclick="cerrarModal()">×</div>

            <h2>Información del Producto</h2>

            <div class="modal-info">
                <p><strong>Nombre:</strong> <span id="modalNombre"></span></p>
                <p><strong>Codigo:</strong> <span id="modalCodigo"></span></p>
                <p><strong>Descripcion:</strong> <span id="modalDescripcion"></span></p>
                <p><strong>Precio:</strong> <span id="modalPrecio"></span></p>
                <p><strong>Stock:</strong> <span id="modalStock"></span></p>
                <p><strong>Categoría:</strong> <span id="modalCategoria"></span></p>
                <p><strong>Estado:</strong> <span id="modalEstado"></span></p>
                <p><strong>Foto:</strong><br>
                    <img id="modalImagen" src="" width="80">
                </p>
            </div>

        </div>
    </div>
   
        
        

</body>
</html>

<?php
$conn->close();
?>